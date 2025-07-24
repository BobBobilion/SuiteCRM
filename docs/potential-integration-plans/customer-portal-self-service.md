# Customer Portal & Self-Service Platform - Implementation Plan

## Overview
Build a comprehensive customer portal that enables self-service capabilities including ticket management, knowledge base access, document sharing, billing management, and real-time communication with support teams.

## Architecture Overview

### Core Components
1. **Portal Framework** - Secure customer-facing web application
2. **Authentication System** - SSO and multi-factor authentication
3. **Ticket Management** - Self-service support ticket system
4. **Knowledge Base** - Searchable help articles and documentation
5. **Document Center** - Secure file sharing and management
6. **Billing Portal** - Invoice viewing and payment processing
7. **Live Chat** - Real-time communication with support
8. **Analytics** - Usage tracking and customer insights

## Detailed Implementation Steps

### Phase 1: Database Schema Design

#### 1.1 Create Database Tables
```sql
-- Portal users (extends contacts)
CREATE TABLE portal_users (
    id char(36) PRIMARY KEY,
    contact_id char(36) NOT NULL,
    username varchar(100) UNIQUE NOT NULL,
    password_hash varchar(255),
    portal_status enum('active','inactive','pending','suspended') DEFAULT 'pending',
    activation_token varchar(255),
    activation_date datetime,
    last_login datetime,
    login_count int DEFAULT 0,
    failed_login_count int DEFAULT 0,
    locked_until datetime,
    two_factor_enabled tinyint(1) DEFAULT 0,
    two_factor_secret varchar(255),
    preferences text, -- JSON user preferences
    api_token varchar(255),
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (contact_id) REFERENCES contacts(id),
    INDEX idx_username (username),
    INDEX idx_contact (contact_id)
);

-- Portal access permissions
CREATE TABLE portal_permissions (
    id char(36) PRIMARY KEY,
    portal_user_id char(36) NOT NULL,
    module varchar(50) NOT NULL,
    permission_type enum('view','create','edit','delete','download') NOT NULL,
    scope enum('own','account','all') DEFAULT 'own',
    conditions text, -- JSON additional conditions
    granted_by char(36),
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (portal_user_id) REFERENCES portal_users(id),
    UNIQUE KEY unique_permission (portal_user_id, module, permission_type)
);

-- Portal sessions
CREATE TABLE portal_sessions (
    id char(36) PRIMARY KEY,
    portal_user_id char(36) NOT NULL,
    session_token varchar(255) UNIQUE NOT NULL,
    ip_address varchar(45),
    user_agent varchar(500),
    device_info text, -- JSON device information
    location_data text, -- JSON geolocation
    created_date datetime,
    last_activity datetime,
    expires_date datetime,
    is_active tinyint(1) DEFAULT 1,
    FOREIGN KEY (portal_user_id) REFERENCES portal_users(id),
    INDEX idx_token (session_token),
    INDEX idx_expires (expires_date)
);

-- Support tickets (portal-specific)
CREATE TABLE portal_tickets (
    id char(36) PRIMARY KEY,
    ticket_number varchar(50) UNIQUE NOT NULL,
    portal_user_id char(36) NOT NULL,
    account_id char(36),
    category varchar(100),
    subcategory varchar(100),
    subject varchar(255) NOT NULL,
    description text,
    priority enum('low','medium','high','urgent') DEFAULT 'medium',
    status enum('new','open','pending','resolved','closed') DEFAULT 'new',
    assigned_user_id char(36),
    resolution text,
    satisfaction_rating int,
    satisfaction_comment text,
    attachments text, -- JSON attachment info
    tags text, -- JSON tags
    sla_deadline datetime,
    first_response_time int, -- minutes
    resolution_time int, -- minutes
    date_entered datetime,
    date_modified datetime,
    date_resolved datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (portal_user_id) REFERENCES portal_users(id),
    INDEX idx_status (status),
    INDEX idx_user (portal_user_id)
);

-- Ticket communications
CREATE TABLE portal_ticket_messages (
    id char(36) PRIMARY KEY,
    ticket_id char(36) NOT NULL,
    sender_type enum('customer','agent','system') NOT NULL,
    sender_id char(36),
    message text NOT NULL,
    attachments text, -- JSON attachments
    is_internal tinyint(1) DEFAULT 0,
    is_read tinyint(1) DEFAULT 0,
    read_date datetime,
    date_entered datetime,
    FOREIGN KEY (ticket_id) REFERENCES portal_tickets(id),
    INDEX idx_ticket (ticket_id)
);

-- Knowledge base articles
CREATE TABLE portal_kb_articles (
    id char(36) PRIMARY KEY,
    title varchar(255) NOT NULL,
    slug varchar(255) UNIQUE NOT NULL,
    content text NOT NULL,
    summary text,
    category_id char(36),
    tags text, -- JSON tags
    status enum('draft','published','archived') DEFAULT 'draft',
    visibility enum('public','portal','internal') DEFAULT 'portal',
    featured tinyint(1) DEFAULT 0,
    helpful_count int DEFAULT 0,
    not_helpful_count int DEFAULT 0,
    view_count int DEFAULT 0,
    search_keywords text,
    related_articles text, -- JSON array of article IDs
    attachments text, -- JSON attachments
    author_id char(36),
    published_date datetime,
    last_updated datetime,
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    INDEX idx_status (status),
    INDEX idx_category (category_id),
    FULLTEXT idx_search (title, content, search_keywords)
);

-- KB categories
CREATE TABLE portal_kb_categories (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    slug varchar(255) UNIQUE NOT NULL,
    description text,
    parent_id char(36),
    icon varchar(50),
    display_order int DEFAULT 0,
    is_active tinyint(1) DEFAULT 1,
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (parent_id) REFERENCES portal_kb_categories(id)
);

-- Document sharing
CREATE TABLE portal_documents (
    id char(36) PRIMARY KEY,
    document_name varchar(255) NOT NULL,
    file_name varchar(255),
    file_path varchar(500),
    file_size int,
    file_type varchar(100),
    category varchar(100),
    description text,
    version varchar(20),
    account_id char(36),
    shared_with text, -- JSON sharing settings
    download_count int DEFAULT 0,
    last_downloaded datetime,
    requires_acknowledgment tinyint(1) DEFAULT 0,
    expiry_date datetime,
    uploaded_by char(36),
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0,
    INDEX idx_account (account_id)
);

-- Portal activity tracking
CREATE TABLE portal_activity_log (
    id char(36) PRIMARY KEY,
    portal_user_id char(36) NOT NULL,
    activity_type varchar(50) NOT NULL,
    module varchar(50),
    record_id char(36),
    activity_data text, -- JSON activity details
    ip_address varchar(45),
    user_agent varchar(500),
    date_entered datetime,
    FOREIGN KEY (portal_user_id) REFERENCES portal_users(id),
    INDEX idx_user_date (portal_user_id, date_entered),
    INDEX idx_type (activity_type)
);

-- Live chat sessions
CREATE TABLE portal_chat_sessions (
    id char(36) PRIMARY KEY,
    portal_user_id char(36) NOT NULL,
    agent_id char(36),
    ticket_id char(36),
    status enum('waiting','active','ended','abandoned') DEFAULT 'waiting',
    queue_position int,
    started_date datetime,
    accepted_date datetime,
    ended_date datetime,
    wait_time int, -- seconds
    duration int, -- seconds
    rating int,
    rating_comment text,
    transcript text, -- JSON chat messages
    date_entered datetime,
    FOREIGN KEY (portal_user_id) REFERENCES portal_users(id),
    INDEX idx_status (status)
);
```

### Phase 2: Portal Framework

#### 2.1 Portal Application Structure
Location: `portal/app/Core/PortalApplication.php`

```php
class PortalApplication {
    private $auth;
    private $router;
    private $middleware = [];
    private $config;
    
    public function __construct() {
        $this->config = new PortalConfig();
        $this->auth = new PortalAuthentication();
        $this->router = new PortalRouter();
        
        $this->registerMiddleware();
        $this->registerRoutes();
    }
    
    private function registerMiddleware() {
        $this->middleware[] = new SecurityMiddleware();
        $this->middleware[] = new AuthenticationMiddleware($this->auth);
        $this->middleware[] = new RateLimitMiddleware();
        $this->middleware[] = new ActivityLoggingMiddleware();
        $this->middleware[] = new LocalizationMiddleware();
    }
    
    public function handle(Request $request) {
        try {
            // Run middleware pipeline
            $response = $this->runMiddleware($request);
            
            if (!$response) {
                // Route request
                $response = $this->router->dispatch($request);
            }
            
            return $response;
            
        } catch (UnauthorizedException $e) {
            return $this->redirectToLogin();
        } catch (NotFoundException $e) {
            return $this->show404();
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->show500();
        }
    }
}
```

#### 2.2 Portal Frontend Framework
Location: `portal/frontend/src/App.jsx`

```jsx
const PortalApp = () => {
    const { user, isAuthenticated } = useAuth();
    const [notifications, setNotifications] = useState([]);
    
    useEffect(() => {
        if (isAuthenticated) {
            // Initialize WebSocket for real-time updates
            const ws = new WebSocket('wss://portal.example.com/ws');
            
            ws.onmessage = (event) => {
                const data = JSON.parse(event.data);
                handleRealtimeUpdate(data);
            };
            
            return () => ws.close();
        }
    }, [isAuthenticated]);
    
    return (
        <Router>
            <div className="portal-app">
                {isAuthenticated ? (
                    <>
                        <PortalHeader user={user} notifications={notifications} />
                        <PortalSidebar />
                        <main className="portal-content">
                            <Routes>
                                <Route path="/" element={<Dashboard />} />
                                <Route path="/tickets/*" element={<TicketModule />} />
                                <Route path="/knowledge/*" element={<KnowledgeBase />} />
                                <Route path="/documents/*" element={<DocumentCenter />} />
                                <Route path="/billing/*" element={<BillingPortal />} />
                                <Route path="/profile/*" element={<UserProfile />} />
                            </Routes>
                        </main>
                    </>
                ) : (
                    <Routes>
                        <Route path="/login" element={<LoginPage />} />
                        <Route path="/register" element={<RegistrationPage />} />
                        <Route path="/forgot-password" element={<ForgotPassword />} />
                        <Route path="*" element={<Navigate to="/login" />} />
                    </Routes>
                )}
            </div>
        </Router>
    );
};
```

### Phase 3: Authentication & Security

#### 3.1 Multi-Factor Authentication
Location: `portal/app/Auth/MFAService.php`

```php
class MFAService {
    private $totpProvider;
    private $smsProvider;
    private $emailProvider;
    
    public function setupMFA($userId, $method = 'totp') {
        switch ($method) {
            case 'totp':
                return $this->setupTOTP($userId);
            case 'sms':
                return $this->setupSMS($userId);
            case 'email':
                return $this->setupEmail($userId);
        }
    }
    
    private function setupTOTP($userId) {
        $secret = $this->totpProvider->generateSecret();
        
        // Store encrypted secret
        $user = PortalUser::find($userId);
        $user->two_factor_secret = encrypt($secret);
        $user->save();
        
        // Generate QR code
        $qrCode = $this->totpProvider->getQRCodeUrl(
            $user->email,
            $secret,
            config('portal.name')
        );
        
        return [
            'method' => 'totp',
            'qr_code' => $qrCode,
            'secret' => $secret,
            'backup_codes' => $this->generateBackupCodes($userId)
        ];
    }
    
    public function verifyMFA($userId, $code, $method = 'totp') {
        $user = PortalUser::find($userId);
        
        switch ($method) {
            case 'totp':
                $secret = decrypt($user->two_factor_secret);
                return $this->totpProvider->verify($code, $secret);
                
            case 'backup':
                return $this->verifyBackupCode($userId, $code);
                
            case 'sms':
                return $this->verifySMSCode($userId, $code);
        }
        
        return false;
    }
}
```

#### 3.2 SSO Integration
Location: `portal/app/Auth/SSOProvider.php`

```php
class SSOProvider {
    private $providers = [];
    
    public function __construct() {
        $this->registerProviders();
    }
    
    private function registerProviders() {
        $this->providers['saml'] = new SAMLProvider();
        $this->providers['oauth'] = new OAuthProvider();
        $this->providers['ldap'] = new LDAPProvider();
        $this->providers['azure_ad'] = new AzureADProvider();
    }
    
    public function authenticate($provider, $credentials) {
        if (!isset($this->providers[$provider])) {
            throw new InvalidProviderException();
        }
        
        $result = $this->providers[$provider]->authenticate($credentials);
        
        if ($result->isSuccess()) {
            // Map external user to portal user
            $portalUser = $this->mapExternalUser($result->getUser(), $provider);
            
            // Create session
            return $this->createPortalSession($portalUser);
        }
        
        return false;
    }
}
```

### Phase 4: Ticket Management System

#### 4.1 Ticket Service
Location: `portal/app/Tickets/TicketService.php`

```php
class TicketService {
    private $slaManager;
    private $notificationService;
    private $autoResponseService;
    
    public function createTicket($data, $userId) {
        DB::beginTransaction();
        
        try {
            $ticket = new PortalTicket();
            $ticket->ticket_number = $this->generateTicketNumber();
            $ticket->portal_user_id = $userId;
            $ticket->subject = $data['subject'];
            $ticket->description = $data['description'];
            $ticket->category = $data['category'];
            $ticket->priority = $this->determinePriority($data);
            
            // Set SLA deadline
            $ticket->sla_deadline = $this->slaManager->calculateDeadline(
                $ticket->priority,
                $ticket->category
            );
            
            $ticket->save();
            
            // Handle attachments
            if (!empty($data['attachments'])) {
                $this->processAttachments($ticket, $data['attachments']);
            }
            
            // Auto-assignment
            $this->autoAssignTicket($ticket);
            
            // Send notifications
            $this->notificationService->notifyNewTicket($ticket);
            
            // Auto-response
            $this->autoResponseService->sendNewTicketResponse($ticket);
            
            DB::commit();
            
            return $ticket;
            
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    private function autoAssignTicket($ticket) {
        $rules = AssignmentRule::where('category', $ticket->category)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();
            
        foreach ($rules as $rule) {
            if ($this->evaluateRule($rule, $ticket)) {
                $agent = $this->selectAgent($rule);
                
                if ($agent) {
                    $ticket->assigned_user_id = $agent->id;
                    $ticket->save();
                    break;
                }
            }
        }
    }
}
```

#### 4.2 Ticket Portal UI
Location: `portal/frontend/src/modules/Tickets/TicketList.jsx`

```jsx
const TicketList = () => {
    const [tickets, setTickets] = useState([]);
    const [filters, setFilters] = useState({
        status: 'all',
        priority: 'all',
        category: 'all'
    });
    const [sortBy, setSortBy] = useState('date_created');
    const [searchTerm, setSearchTerm] = useState('');
    
    const filteredTickets = useMemo(() => {
        return tickets.filter(ticket => {
            const matchesSearch = ticket.subject.toLowerCase().includes(searchTerm.toLowerCase()) ||
                                ticket.ticket_number.includes(searchTerm);
            const matchesStatus = filters.status === 'all' || ticket.status === filters.status;
            const matchesPriority = filters.priority === 'all' || ticket.priority === filters.priority;
            const matchesCategory = filters.category === 'all' || ticket.category === filters.category;
            
            return matchesSearch && matchesStatus && matchesPriority && matchesCategory;
        });
    }, [tickets, filters, searchTerm]);
    
    return (
        <div className="ticket-list">
            <div className="ticket-header">
                <h1>Support Tickets</h1>
                <Button onClick={() => navigate('/tickets/new')} variant="primary">
                    Create New Ticket
                </Button>
            </div>
            
            <TicketFilters
                filters={filters}
                onFilterChange={setFilters}
                onSearch={setSearchTerm}
            />
            
            <div className="ticket-table">
                <table>
                    <thead>
                        <tr>
                            <th onClick={() => setSortBy('ticket_number')}>Ticket #</th>
                            <th onClick={() => setSortBy('subject')}>Subject</th>
                            <th onClick={() => setSortBy('category')}>Category</th>
                            <th onClick={() => setSortBy('priority')}>Priority</th>
                            <th onClick={() => setSortBy('status')}>Status</th>
                            <th onClick={() => setSortBy('date_created')}>Created</th>
                            <th onClick={() => setSortBy('last_updated')}>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        {filteredTickets.map(ticket => (
                            <TicketRow
                                key={ticket.id}
                                ticket={ticket}
                                onClick={() => navigate(`/tickets/${ticket.id}`)}
                            />
                        ))}
                    </tbody>
                </table>
            </div>
            
            <Pagination
                total={filteredTickets.length}
                pageSize={20}
                onPageChange={handlePageChange}
            />
        </div>
    );
};

const TicketDetail = ({ ticketId }) => {
    const [ticket, setTicket] = useState(null);
    const [messages, setMessages] = useState([]);
    const [newMessage, setNewMessage] = useState('');
    const [attachments, setAttachments] = useState([]);
    
    return (
        <div className="ticket-detail">
            <TicketHeader ticket={ticket} />
            
            <div className="ticket-content">
                <div className="ticket-main">
                    <MessageThread
                        messages={messages}
                        onReply={handleReply}
                    />
                    
                    <MessageComposer
                        value={newMessage}
                        onChange={setNewMessage}
                        onSend={handleSend}
                        onAttach={handleAttach}
                        attachments={attachments}
                    />
                </div>
                
                <div className="ticket-sidebar">
                    <TicketInfo ticket={ticket} />
                    <TicketActions
                        ticket={ticket}
                        onStatusChange={handleStatusChange}
                        onClose={handleClose}
                    />
                    <RelatedArticles ticket={ticket} />
                </div>
            </div>
        </div>
    );
};
```

### Phase 5: Knowledge Base

#### 5.1 Knowledge Base Search
Location: `portal/app/KnowledgeBase/SearchService.php`

```php
class KnowledgeBaseSearchService {
    private $elasticClient;
    private $mlRanker;
    
    public function search($query, $filters = []) {
        // Build Elasticsearch query
        $esQuery = [
            'bool' => [
                'must' => [
                    [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => ['title^3', 'content', 'tags^2', 'search_keywords^2'],
                            'type' => 'best_fields',
                            'fuzziness' => 'AUTO'
                        ]
                    ]
                ],
                'filter' => $this->buildFilters($filters)
            ]
        ];
        
        // Search
        $results = $this->elasticClient->search([
            'index' => 'portal_kb',
            'body' => [
                'query' => $esQuery,
                'highlight' => [
                    'fields' => [
                        'content' => ['fragment_size' => 150]
                    ]
                ],
                'size' => 50
            ]
        ]);
        
        // ML-based re-ranking
        $articles = $this->extractArticles($results);
        $rankedArticles = $this->mlRanker->rank($articles, $query);
        
        // Track search for analytics
        $this->trackSearch($query, count($rankedArticles));
        
        return $rankedArticles;
    }
    
    public function getSuggestions($query) {
        return $this->elasticClient->search([
            'index' => 'portal_kb',
            'body' => [
                'suggest' => [
                    'article-suggest' => [
                        'prefix' => $query,
                        'completion' => [
                            'field' => 'title.suggest',
                            'size' => 5
                        ]
                    ]
                ]
            ]
        ]);
    }
}
```

#### 5.2 Knowledge Base UI
Location: `portal/frontend/src/modules/KnowledgeBase/KnowledgeBase.jsx`

```jsx
const KnowledgeBase = () => {
    const [searchQuery, setSearchQuery] = useState('');
    const [categories, setCategories] = useState([]);
    const [featuredArticles, setFeaturedArticles] = useState([]);
    const [searchResults, setSearchResults] = useState(null);
    
    const handleSearch = async (query) => {
        const results = await searchKnowledgeBase(query);
        setSearchResults(results);
    };
    
    return (
        <div className="knowledge-base">
            <div className="kb-header">
                <h1>Knowledge Base</h1>
                <KBSearchBar
                    value={searchQuery}
                    onChange={setSearchQuery}
                    onSearch={handleSearch}
                    placeholder="Search for answers..."
                />
            </div>
            
            {searchResults ? (
                <SearchResults
                    results={searchResults}
                    query={searchQuery}
                    onClear={() => setSearchResults(null)}
                />
            ) : (
                <>
                    <FeaturedArticles articles={featuredArticles} />
                    
                    <CategoryGrid categories={categories} />
                    
                    <PopularArticles limit={10} />
                    
                    <RecentlyUpdated limit={5} />
                </>
            )}
        </div>
    );
};

const ArticleViewer = ({ articleId }) => {
    const [article, setArticle] = useState(null);
    const [relatedArticles, setRelatedArticles] = useState([]);
    const [feedback, setFeedback] = useState(null);
    
    const handleFeedback = async (helpful) => {
        await submitArticleFeedback(articleId, helpful);
        setFeedback(helpful);
    };
    
    return (
        <div className="article-viewer">
            <Breadcrumb path={article?.breadcrumb} />
            
            <article className="kb-article">
                <h1>{article?.title}</h1>
                
                <ArticleMeta
                    author={article?.author}
                    lastUpdated={article?.last_updated}
                    readTime={article?.read_time}
                />
                
                <div className="article-content" 
                     dangerouslySetInnerHTML={{ __html: article?.content }} />
                
                {article?.attachments?.length > 0 && (
                    <AttachmentList attachments={article.attachments} />
                )}
                
                <ArticleFeedback
                    onFeedback={handleFeedback}
                    currentFeedback={feedback}
                />
                
                <RelatedArticles articles={relatedArticles} />
            </article>
        </div>
    );
};
```

### Phase 6: Document Management

#### 6.1 Document Service
Location: `portal/app/Documents/DocumentService.php`

```php
class DocumentService {
    private $storage;
    private $encryptor;
    private $scanner;
    
    public function uploadDocument($file, $metadata, $userId) {
        // Validate file
        $this->validateFile($file);
        
        // Scan for viruses
        if (!$this->scanner->scan($file)) {
            throw new SecurityException('File failed security scan');
        }
        
        // Generate secure filename
        $filename = $this->generateSecureFilename($file);
        
        // Encrypt if sensitive
        if ($metadata['is_sensitive'] ?? false) {
            $encryptedFile = $this->encryptor->encryptFile($file);
            $file = $encryptedFile;
        }
        
        // Store file
        $path = $this->storage->store($file, 'portal/documents/' . $userId);
        
        // Create document record
        $document = new PortalDocument();
        $document->document_name = $metadata['name'];
        $document->file_name = $filename;
        $document->file_path = $path;
        $document->file_size = $file->getSize();
        $document->file_type = $file->getMimeType();
        $document->category = $metadata['category'] ?? 'general';
        $document->description = $metadata['description'] ?? '';
        $document->save();
        
        // Set permissions
        $this->setDocumentPermissions($document, $metadata['permissions'] ?? []);
        
        return $document;
    }
    
    public function getDocumentUrl($documentId, $userId) {
        $document = $this->getDocument($documentId);
        
        // Check permissions
        if (!$this->hasAccess($document, $userId)) {
            throw new UnauthorizedException();
        }
        
        // Generate signed URL
        return $this->storage->temporaryUrl(
            $document->file_path,
            now()->addMinutes(30),
            [
                'ResponseContentDisposition' => 'attachment; filename="' . $document->file_name . '"'
            ]
        );
    }
}
```

### Phase 7: Billing Portal

#### 7.1 Billing Integration
Location: `portal/app/Billing/BillingService.php`

```php
class BillingService {
    private $paymentGateway;
    private $invoiceGenerator;
    
    public function getInvoices($accountId) {
        $invoices = Invoice::where('account_id', $accountId)
            ->where('status', '!=', 'draft')
            ->orderBy('date_entered', 'desc')
            ->get();
            
        return $invoices->map(function($invoice) {
            return [
                'id' => $invoice->id,
                'number' => $invoice->invoice_number,
                'date' => $invoice->invoice_date,
                'due_date' => $invoice->due_date,
                'amount' => $invoice->total_amount,
                'status' => $invoice->payment_status,
                'pdf_url' => $this->getPdfUrl($invoice),
                'can_pay_online' => $this->canPayOnline($invoice)
            ];
        });
    }
    
    public function processPayment($invoiceId, $paymentMethod, $amount) {
        $invoice = Invoice::find($invoiceId);
        
        try {
            // Process payment through gateway
            $result = $this->paymentGateway->charge([
                'amount' => $amount,
                'currency' => $invoice->currency,
                'payment_method' => $paymentMethod,
                'description' => "Invoice #{$invoice->invoice_number}",
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'account_id' => $invoice->account_id
                ]
            ]);
            
            if ($result->success) {
                // Update invoice
                $invoice->payment_status = 'paid';
                $invoice->payment_date = now();
                $invoice->payment_reference = $result->transaction_id;
                $invoice->save();
                
                // Create payment record
                $this->createPaymentRecord($invoice, $result);
                
                // Send confirmation
                $this->sendPaymentConfirmation($invoice);
                
                return ['success' => true, 'transaction_id' => $result->transaction_id];
            }
            
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);
            
            throw new PaymentException('Payment processing failed');
        }
    }
}
```

### Phase 8: Live Chat System

#### 8.1 Chat Service
Location: `portal/app/Chat/ChatService.php`

```php
class ChatService {
    private $queueManager;
    private $agentRouter;
    private $chatBot;
    
    public function initiateChat($userId, $initialMessage = null) {
        // Check if user has active chat
        $activeChat = $this->getActiveChat($userId);
        if ($activeChat) {
            return $activeChat;
        }
        
        // Create new chat session
        $chat = new PortalChatSession();
        $chat->portal_user_id = $userId;
        $chat->status = 'waiting';
        
        // Try chatbot first
        if ($this->chatBot->canHandle($initialMessage)) {
            $chat->status = 'bot';
            $chat->save();
            
            $this->chatBot->handleMessage($chat, $initialMessage);
            return $chat;
        }
        
        // Queue for human agent
        $queuePosition = $this->queueManager->addToQueue($chat);
        $chat->queue_position = $queuePosition;
        $chat->save();
        
        // Notify available agents
        $this->notifyAgents($chat);
        
        return $chat;
    }
    
    public function sendMessage($chatId, $message, $senderId, $senderType = 'customer') {
        $chat = PortalChatSession::find($chatId);
        
        // Store message
        $chatMessage = [
            'id' => uniqid(),
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message' => $message,
            'timestamp' => time()
        ];
        
        $transcript = json_decode($chat->transcript, true) ?? [];
        $transcript[] = $chatMessage;
        $chat->transcript = json_encode($transcript);
        $chat->save();
        
        // Broadcast to participants
        $this->broadcastMessage($chat, $chatMessage);
        
        // Update last activity
        $this->updateActivity($chat);
        
        return $chatMessage;
    }
}
```

#### 8.2 Live Chat UI
Location: `portal/frontend/src/modules/Chat/LiveChat.jsx`

```jsx
const LiveChatWidget = () => {
    const [isOpen, setIsOpen] = useState(false);
    const [chat, setChat] = useState(null);
    const [messages, setMessages] = useState([]);
    const [inputMessage, setInputMessage] = useState('');
    const [isTyping, setIsTyping] = useState(false);
    
    useEffect(() => {
        if (chat?.id) {
            // Connect to WebSocket
            const ws = new WebSocket(`wss://portal.example.com/chat/${chat.id}`);
            
            ws.onmessage = (event) => {
                const data = JSON.parse(event.data);
                
                switch (data.type) {
                    case 'message':
                        setMessages(prev => [...prev, data.message]);
                        break;
                    case 'typing':
                        setIsTyping(data.isTyping);
                        break;
                    case 'agent_joined':
                        handleAgentJoined(data.agent);
                        break;
                }
            };
            
            return () => ws.close();
        }
    }, [chat]);
    
    const initiateChat = async () => {
        const newChat = await startChat();
        setChat(newChat);
        setIsOpen(true);
    };
    
    const sendMessage = () => {
        if (inputMessage.trim()) {
            ws.send(JSON.stringify({
                type: 'message',
                message: inputMessage
            }));
            
            setInputMessage('');
        }
    };
    
    return (
        <>
            {!isOpen && (
                <button className="chat-bubble" onClick={initiateChat}>
                    <ChatIcon />
                    Need Help?
                </button>
            )}
            
            {isOpen && (
                <div className="chat-widget">
                    <ChatHeader
                        chat={chat}
                        onClose={() => setIsOpen(false)}
                    />
                    
                    <ChatMessages
                        messages={messages}
                        isTyping={isTyping}
                    />
                    
                    <ChatInput
                        value={inputMessage}
                        onChange={setInputMessage}
                        onSend={sendMessage}
                        disabled={chat?.status === 'ended'}
                    />
                </div>
            )}
        </>
    );
};
```

### Phase 9: Analytics & Reporting

#### 9.1 Portal Analytics Service
Location: `portal/app/Analytics/PortalAnalytics.php`

```php
class PortalAnalytics {
    public function trackActivity($userId, $activity, $data = []) {
        $log = new PortalActivityLog();
        $log->portal_user_id = $userId;
        $log->activity_type = $activity;
        $log->activity_data = json_encode($data);
        $log->ip_address = request()->ip();
        $log->user_agent = request()->userAgent();
        $log->save();
        
        // Real-time analytics
        $this->publishToAnalytics($activity, $data);
    }
    
    public function getUserMetrics($userId, $dateRange = null) {
        return [
            'login_count' => $this->getLoginCount($userId, $dateRange),
            'tickets_created' => $this->getTicketCount($userId, $dateRange),
            'kb_articles_viewed' => $this->getKBViewCount($userId, $dateRange),
            'documents_downloaded' => $this->getDocumentDownloads($userId, $dateRange),
            'avg_session_duration' => $this->getAvgSessionDuration($userId, $dateRange),
            'last_activity' => $this->getLastActivity($userId)
        ];
    }
    
    public function getPortalMetrics($dateRange = null) {
        return [
            'active_users' => $this->getActiveUserCount($dateRange),
            'total_sessions' => $this->getSessionCount($dateRange),
            'ticket_metrics' => [
                'created' => $this->getTicketsCreated($dateRange),
                'resolved' => $this->getTicketsResolved($dateRange),
                'avg_resolution_time' => $this->getAvgResolutionTime($dateRange),
                'satisfaction_score' => $this->getAvgSatisfactionScore($dateRange)
            ],
            'kb_metrics' => [
                'article_views' => $this->getKBViews($dateRange),
                'helpful_rate' => $this->getKBHelpfulRate($dateRange),
                'popular_articles' => $this->getPopularArticles($dateRange)
            ],
            'usage_patterns' => $this->getUsagePatterns($dateRange)
        ];
    }
}
```

### Phase 10: Mobile Optimization

#### 10.1 Progressive Web App
Location: `portal/frontend/src/serviceWorker.js`

```javascript
// Service Worker for offline functionality
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('portal-v1').then((cache) => {
            return cache.addAll([
                '/',
                '/offline.html',
                '/css/app.css',
                '/js/app.js',
                '/img/logo.png'
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            // Cache hit - return response
            if (response) {
                return response;
            }
            
            return fetch(event.request).then((response) => {
                // Check if valid response
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }
                
                // Clone and cache the response
                const responseToCache = response.clone();
                
                caches.open('portal-v1').then((cache) => {
                    cache.put(event.request, responseToCache);
                });
                
                return response;
            });
        }).catch(() => {
            // Offline fallback
            return caches.match('/offline.html');
        })
    );
});
```

## Development Timeline

### Week 1-2: Core Infrastructure
- Portal framework setup
- Database schema implementation
- Basic authentication system

### Week 3-4: Authentication & Security
- MFA implementation
- SSO integration
- Session management

### Week 5-6: Ticket Management
- Ticket creation and listing
- Message threading
- Auto-assignment rules

### Week 7-8: Knowledge Base
- Article management
- Search functionality
- Category navigation

### Week 9-10: Document Management
- File upload/download
- Permission system
- Version control

### Week 11-12: Billing Portal
- Invoice viewing
- Payment processing
- Payment history

### Week 13-14: Live Chat
- Chat infrastructure
- Agent routing
- Chat widget UI

### Week 15-16: Analytics & Mobile
- Activity tracking
- Analytics dashboard
- PWA implementation
- Testing and optimization

## Technical Dependencies
- Laravel/Symfony for backend
- React for frontend
- WebSockets for real-time features
- Elasticsearch for knowledge base search
- Redis for caching and sessions
- Stripe/PayPal for payments

## Success Metrics
1. 60% ticket deflection through self-service
2. <30 second average chat wait time
3. 90% customer satisfaction score
4. 50% reduction in support costs
5. 80% mobile usage adoption