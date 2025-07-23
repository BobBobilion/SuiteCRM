# Themes Directory

The themes directory contains the visual presentation layers for SuiteCRM, including stylesheets, images, fonts, and theme configurations. Themes control the look and feel of the CRM interface.

## Available Themes

### SuiteP/
The modern, responsive default theme for SuiteCRM 7.x:

#### css/
Stylesheet files:
- **bootstrap.min.css** - Bootstrap framework styles
- **fonts.css** - Font definitions
- **footable.core.css** - Responsive table styles
- **grid.css** - Grid layout system
- **normalize.css** - Browser normalization
- **dashboardstyle.css** - Dashboard-specific styles
- **studio.css** - Studio module styles
- **wizard.css** - Wizard interface styles
- **print.css** - Print media styles
- **deprecated.css** - Legacy style support
- **chart.css** - Chart styling
- **bubbles.css** - Notification bubbles
- **colourSelector.php** - Dynamic color selection

#### fonts/
Web fonts and icon fonts:
- **Lato-*** - Lato font family files (Regular, Bold, Italic, etc.)
- **glyphicons-halflings-*** - Bootstrap Glyphicons
- **footable.*** - Footable table icons
- Various Google Fonts web font files (.woff2)

#### images/
Theme images and icons:
- Module icons (SVG and PNG formats)
  - Accounts.svg, Contacts.svg, Leads.svg, etc.
- UI elements
  - arrows, buttons, backgrounds
  - calendar navigation
  - attachment indicators
  - action icons
- Logo files
  - suite_logo.png
  - suitecrm_login.png

#### js/
Theme-specific JavaScript:
- **style.js** - Theme behavior and interactions

#### less/
LESS source files for CSS generation:
- Bootstrap component files
- SuiteCRM customizations
- **variables.less** - Theme variables
- **sugar.less** - Core SuiteCRM styles
- **sugarmobile.less** - Mobile-specific styles

#### themedef.php
Theme definition file containing:
- Theme metadata
- Color schemes
- Configuration options

### default/
Legacy theme for backward compatibility:

#### css/
Core stylesheets:
- **bootstrap.css** - Bootstrap framework
- **style.css** - Main theme styles
- **wizard.css** - Wizard styles
- **deprecated.css** - Legacy support
- **print.css** - Print styles
- **chart.css** - Chart styles

#### font/
Font files:
- **fontawesome-webfont.*** - Font Awesome icons

#### images/
Extensive image library:
- Module icons (.gif format)
  - icon_[ModuleName].gif (32x32 and 16x16)
  - Create[ModuleName].gif
- UI elements
  - Buttons and backgrounds
  - Arrows and indicators
  - Form elements
- Legacy Sugar theme images
- Dashboard elements

## Theme Structure

### Common Elements

#### Module Icons
Each module has associated icons:
- Standard icon (e.g., Accounts.png/svg)
- Create icon (e.g., CreateAccounts.gif)
- 32x32 and 16x16 variants
- Favicon versions

#### UI Components
- Navigation elements
- Form controls
- Buttons and actions
- Dashboard widgets
- Calendar elements

#### Color Schemes
Themes support multiple color variations:
- Blue (default)
- Gray
- Green
- Orange
- Purple
- Red

### File Types

#### Stylesheets
- **.css** - Compiled stylesheets
- **.less** - LESS source files

#### Images
- **.png** - Module icons and UI elements
- **.svg** - Scalable vector graphics
- **.gif** - Legacy icons
- **.jpg** - Photos and complex images

#### Fonts
- **.ttf** - TrueType fonts
- **.woff/.woff2** - Web font formats
- **.eot** - Embedded OpenType
- **.svg** - SVG fonts

## Theme Configuration

### themedef.php
Defines theme properties:
```php
$themedef = array(
    'name' => 'Theme Name',
    'description' => 'Theme Description',
    'version' => array(
        'regex_matches' => array('version'),
    ),
    'group_tabs' => true,
    'colors' => array(...),
);
```

### Customization
- Themes can be customized via Admin > Themes
- Custom themes can be created by copying an existing theme
- CSS can be modified directly or via LESS compilation
- Images can be replaced to rebrand the interface

## Responsive Design
- SuiteP theme is fully responsive
- Mobile-optimized styles
- Touch-friendly interface elements
- Flexible grid system

## Best Practices
1. Create custom themes by extending existing ones
2. Use LESS variables for consistent styling
3. Optimize images for web performance
4. Test across browsers and devices
5. Maintain upgrade-safe customizations

The themes directory provides complete control over the visual presentation of SuiteCRM, allowing for branding and user experience customization.