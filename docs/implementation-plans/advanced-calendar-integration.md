# Advanced Calendar Integration & Scheduling - Implementation Plan

## Overview
Implement comprehensive two-way calendar synchronization with Google Calendar and Outlook, including availability-based scheduling, conflict detection, and automated meeting room booking.

## Architecture Overview

### Core Components
1. **Calendar Sync Engine** - Bidirectional synchronization system
2. **OAuth Integration** - Secure authentication for external calendars
3. **Availability Engine** - Real-time availability checking
4. **Conflict Resolver** - Intelligent conflict detection and resolution
5. **Meeting Room Manager** - Resource booking system

## Detailed Implementation Steps

### Phase 1: Database Schema Design

#### 1.1 Create Database Tables
```sql
-- Calendar accounts configuration
CREATE TABLE calendar_accounts (
    id char(36) PRIMARY KEY,
    user_id char(36) NOT NULL,
    provider enum('google','outlook','exchange') NOT NULL,
    account_email varchar(255),
    access_token text,
    refresh_token text,
    token_expiry datetime,
    sync_enabled tinyint(1) DEFAULT 1,
    last_sync datetime,
    sync_state text, -- JSON sync tokens/cursors
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Calendar sync mapping
CREATE TABLE calendar_sync_map (
    id char(36) PRIMARY KEY,
    calendar_account_id char(36) NOT NULL,
    local_id char(36) NOT NULL, -- SuiteCRM meeting/call ID
    remote_id varchar(255) NOT NULL, -- External calendar event ID
    local_type enum('Meetings','Calls') NOT NULL,
    last_local_modified datetime,
    last_remote_modified datetime,
    sync_hash varchar(64), -- For change detection
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (calendar_account_id) REFERENCES calendar_accounts(id)
);

-- Meeting rooms/resources
CREATE TABLE meeting_rooms (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    location varchar(255),
    capacity int,
    equipment text, -- JSON array of equipment
    calendar_id varchar(255), -- External calendar ID if synced
    status enum('active','maintenance','inactive') DEFAULT 'active',
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0
);

-- Room bookings
CREATE TABLE room_bookings (
    id char(36) PRIMARY KEY,
    room_id char(36) NOT NULL,
    meeting_id char(36),
    start_datetime datetime NOT NULL,
    end_datetime datetime NOT NULL,
    status enum('confirmed','tentative','cancelled') DEFAULT 'confirmed',
    created_by char(36),
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (room_id) REFERENCES meeting_rooms(id),
    FOREIGN KEY (meeting_id) REFERENCES meetings(id)
);

-- User availability preferences
CREATE TABLE user_availability (
    id char(36) PRIMARY KEY,
    user_id char(36) NOT NULL,
    day_of_week tinyint, -- 0-6, NULL for exceptions
    start_time time,
    end_time time,
    availability_type enum('working','busy','out_of_office'),
    specific_date date, -- For exceptions/specific dates
    recurring tinyint(1) DEFAULT 1,
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Phase 2: OAuth Integration Layer

#### 2.1 Google Calendar OAuth
Location: `modules/CalendarSync/OAuth/GoogleProvider.php`

```php
class GoogleProvider {
    private $client;
    
    public function __construct() {
        $this->client = new Google_Client();
        $this->client->setClientId(GOOGLE_CLIENT_ID);
        $this->client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $this->client->setRedirectUri(GOOGLE_REDIRECT_URI);
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
    }
    
    public function getAuthUrl() {
        return $this->client->createAuthUrl();
    }
    
    public function handleCallback($code) {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        // Store tokens in calendar_accounts table
    }
}
```

#### 2.2 Outlook/Exchange OAuth
Location: `modules/CalendarSync/OAuth/OutlookProvider.php`

```php
class OutlookProvider {
    private $provider;
    
    public function __construct() {
        $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => OUTLOOK_CLIENT_ID,
            'clientSecret' => OUTLOOK_CLIENT_SECRET,
            'redirectUri' => OUTLOOK_REDIRECT_URI,
            'urlAuthorize' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'urlAccessToken' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => '',
            'scopes' => 'calendars.readwrite offline_access'
        ]);
    }
}
```

### Phase 3: Calendar Sync Engine

#### 3.1 Core Sync Engine
Location: `modules/CalendarSync/Engine/SyncEngine.php`

```php
class SyncEngine {
    private $providers = [];
    private $conflictResolver;
    
    public function syncUserCalendars($userId) {
        $accounts = $this->getActiveCalendarAccounts($userId);
        
        foreach ($accounts as $account) {
            $provider = $this->getProvider($account->provider);
            $this->syncAccount($account, $provider);
        }
    }
    
    private function syncAccount($account, $provider) {
        // 1. Pull remote changes
        $remoteChanges = $provider->getChanges($account->sync_state);
        
        // 2. Get local changes
        $localChanges = $this->getLocalChanges($account->last_sync);
        
        // 3. Resolve conflicts
        $resolved = $this->conflictResolver->resolve($localChanges, $remoteChanges);
        
        // 4. Apply changes
        $this->applyChanges($resolved);
        
        // 5. Update sync state
        $this->updateSyncState($account, $provider->getSyncToken());
    }
}
```

#### 3.2 Provider Implementations
Location: `modules/CalendarSync/Providers/`

- `GoogleCalendarProvider.php` - Google Calendar API implementation
- `OutlookCalendarProvider.php` - Microsoft Graph API implementation
- `ExchangeProvider.php` - Exchange Web Services implementation

### Phase 4: Availability Engine

#### 4.1 Availability Calculator
Location: `modules/CalendarSync/Availability/AvailabilityEngine.php`

```php
class AvailabilityEngine {
    public function findAvailableSlots($participants, $duration, $dateRange, $constraints = []) {
        $slots = [];
        
        // 1. Get working hours for all participants
        $workingHours = $this->getWorkingHours($participants);
        
        // 2. Get busy times from all calendars
        $busyTimes = $this->getBusyTimes($participants, $dateRange);
        
        // 3. Get room availability if required
        if (!empty($constraints['require_room'])) {
            $roomAvailability = $this->getRoomAvailability($dateRange, $constraints);
        }
        
        // 4. Calculate intersection of available times
        $slots = $this->calculateAvailableSlots(
            $workingHours,
            $busyTimes,
            $duration,
            $roomAvailability ?? null
        );
        
        return $slots;
    }
}
```

#### 4.2 Real-time Availability API
Location: `Api/V8/Controller/AvailabilityController.php`

```php
class AvailabilityController extends BaseController {
    public function checkAvailability(Request $request, Response $response, array $args) {
        $params = $request->getParsedBody();
        
        $engine = new AvailabilityEngine();
        $slots = $engine->findAvailableSlots(
            $params['participants'],
            $params['duration'],
            $params['date_range'],
            $params['constraints'] ?? []
        );
        
        return $response->withJson([
            'data' => [
                'type' => 'availability',
                'attributes' => [
                    'slots' => $slots,
                    'timezone' => $params['timezone']
                ]
            ]
        ]);
    }
}
```

### Phase 5: Conflict Detection & Resolution

#### 5.1 Conflict Detector
Location: `modules/CalendarSync/Conflict/ConflictDetector.php`

```php
class ConflictDetector {
    public function detectConflicts($event) {
        $conflicts = [];
        
        // 1. Time conflicts
        $timeConflicts = $this->checkTimeConflicts($event);
        
        // 2. Resource conflicts
        $resourceConflicts = $this->checkResourceConflicts($event);
        
        // 3. Participant conflicts
        $participantConflicts = $this->checkParticipantConflicts($event);
        
        return array_merge($timeConflicts, $resourceConflicts, $participantConflicts);
    }
}
```

#### 5.2 Conflict Resolution UI
Location: `modules/CalendarSync/javascript/conflict-resolver/`

React component for visual conflict resolution:
- Side-by-side comparison
- One-click resolution options
- Bulk conflict handling

### Phase 6: Meeting Room Management

#### 6.1 Room Booking Engine
Location: `modules/MeetingRooms/BookingEngine.php`

```php
class BookingEngine {
    public function bookRoom($roomId, $meetingId, $startTime, $endTime) {
        // 1. Check availability
        if (!$this->isRoomAvailable($roomId, $startTime, $endTime)) {
            throw new RoomNotAvailableException();
        }
        
        // 2. Create booking
        $booking = new RoomBooking();
        $booking->room_id = $roomId;
        $booking->meeting_id = $meetingId;
        $booking->start_datetime = $startTime;
        $booking->end_datetime = $endTime;
        $booking->save();
        
        // 3. Update external calendar if connected
        $this->syncToExternalCalendar($booking);
        
        return $booking;
    }
}
```

#### 6.2 Room Management UI
Location: `modules/MeetingRooms/views/`

- Room list view with availability indicators
- Room detail view with equipment list
- Booking calendar view
- Administrative settings

### Phase 7: Timezone Handling

#### 7.1 Timezone Service
Location: `modules/CalendarSync/Services/TimezoneService.php`

```php
class TimezoneService {
    public function convertToUserTimezone($datetime, $fromTz, $toTz) {
        $dt = new DateTime($datetime, new DateTimeZone($fromTz));
        $dt->setTimezone(new DateTimeZone($toTz));
        return $dt->format('Y-m-d H:i:s');
    }
    
    public function detectTimezoneFromLocation($location) {
        // Use geocoding API to detect timezone
    }
}
```

### Phase 8: Testing Strategy

#### 8.1 Integration Tests
Location: `tests/integration/modules/CalendarSync/`

**Test Scenarios:**
1. **OAuth Flow**
   - Test Google OAuth authentication
   - Test Outlook OAuth authentication
   - Token refresh handling

2. **Sync Operations**
   - Create meeting in SuiteCRM → Verify in Google Calendar
   - Update event in Outlook → Verify in SuiteCRM
   - Delete event → Verify bidirectional deletion

3. **Conflict Scenarios**
   - Simultaneous updates from both sides
   - Network failure during sync
   - Invalid token handling

4. **Availability Calculation**
   - Multiple participants across timezones
   - Working hours consideration
   - Room availability integration

### Phase 9: Performance Optimization

#### 9.1 Caching Strategy
- Cache user availability for 5 minutes
- Cache calendar data with smart invalidation
- Implement incremental sync using sync tokens

#### 9.2 Batch Operations
- Batch API calls for multiple events
- Queue sync operations for better performance
- Implement webhook support for real-time updates

### Phase 10: Security Considerations

#### 10.1 Token Security
- Encrypt OAuth tokens in database
- Implement token rotation
- Add token expiry monitoring

#### 10.2 Access Control
- Calendar-level permissions
- Room booking approval workflows
- Audit trail for all sync operations

## Development Timeline

### Week 1-2: OAuth Integration
- Implement Google OAuth flow
- Implement Outlook OAuth flow
- Token management system

### Week 3-4: Basic Sync Engine
- Pull calendar events
- Push local changes
- Sync state management

### Week 5-6: Availability System
- Working hours configuration
- Availability calculation algorithm
- API endpoints

### Week 7-8: Conflict Resolution
- Conflict detection logic
- Resolution UI
- Testing conflict scenarios

### Week 9-10: Room Management
- Room booking system
- Resource calendar integration
- Administrative interface

### Week 11-12: Polish & Testing
- Timezone handling refinement
- Performance optimization
- Comprehensive testing

## Technical Dependencies
- Google Calendar API Client
- Microsoft Graph SDK
- League OAuth2 Client
- React for conflict resolution UI
- FullCalendar for calendar views

## Success Metrics
1. 95% sync reliability
2. <5 second availability calculation
3. Zero data loss in conflicts
4. 80% reduction in scheduling time