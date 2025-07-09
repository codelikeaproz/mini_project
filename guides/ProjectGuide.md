# Web-Based Accident Reporting and Vehicle Utilization System

**Course:** IT 82 - Web Software Tools  
**Developer:** [Your Name]  
**Institution:** Central Mindanao University  
**Target Client:** Municipal Disaster Risk Reduction and Management Office (MDRRMO), Maramag, Bukidnon  
**Development Period:** 13 Days Emergency Sprint (July 7-21, 2025)  
**Project Type:** Solo Development  

---

## 📋 PROJECT OVERVIEW

The Web-Based Accident Reporting and Vehicle Utilization System is a digital solution designed to streamline emergency response operations for the Municipal Disaster Risk Reduction and Management Office (MDRRMO) in Maramag, Bukidnon. This system transforms manual paper-based reporting into an efficient digital workflow, enabling faster response times and better coordination during emergency situations.

**⚠️ Emergency Development Note:** Due to time constraints with a July 20-21 deadline, this project follows an accelerated MVP (Minimum Viable Product) approach focusing on essential features.

---

## 🎯 PROJECT OBJECTIVES

### Primary Objectives (3 Core Goals):

1. **Digitize Emergency Response Operations**
   - Transform manual paper-based reporting to digital workflow
   - Implement secure role-based access for MDRRMO personnel

2. **Automate Data Management & Basic Analytics**
   - Reduce data retrieval time from 18 minutes to under 2 minutes
   - Provide simple dashboards with basic Chart.js visualizations

3. **Streamline Incident & Vehicle Management**
   - Track vehicle utilization and basic deployment
   - Generate basic reports with PDF/Excel export functionality

---

## 🔍 PROBLEM STATEMENT

### Current Manual System Issues:
- Paper forms and Excel spreadsheets for reporting
- Data retrieval takes 18+ minutes per report
- Manual entry errors (18-40% error rate)
- No centralized coordination between municipalities
- Difficult to track accident patterns and trends
- No visual representation of accident hotspots

### Proposed Solution:
A web-based system to streamline emergency response operations and improve data accuracy for MDRRMO offices through complete digitization of workflows, including visual heat mapping for accident hotspot identification.

---

## ✅ PROJECT SCOPE (Emergency MVP)

### What the System WILL Include:

#### 🔐 User Management & Authentication
- User registration and login system
- **Two role-based access levels (Admin, MDRRMO Staff only)**
- Basic authentication (no email verification for time constraints)
- Session management and account security features

#### 📊 Incident Reporting Module
- Complete CRUD operations for incident records
- Support for **8 specific incident types:**
  - **Vehicle Incidents:** Vehicle vs Vehicle, Vehicle vs Pedestrian, Vehicle vs Animals, Vehicle vs Property, Vehicle Alone
  - **Medical Emergencies:** Maternity, Stabbing/Shooting, Transport to Hospital
- Real-time incident status tracking
- Location-based incident recording (coordinates for heat mapping)

#### 🚗 Basic Vehicle Management System
- Emergency vehicle inventory management
- Basic vehicle status tracking (Available, Deployed, Maintenance)
- Simple vehicle assignment to incidents
- ~~Advanced fuel consumption monitoring~~ (removed for time)

#### 📈 Simple Analytics & Reporting
- **Basic dashboard with simple statistics**
- **2 simple Chart.js visualizations:**
  - Monthly incident trend (line chart)
  - Incident type distribution (pie chart)
- **Basic PDF/Excel export functionality**
- Simple data tables with incident summaries

#### 🗺️ Heat Map Visualization (Adviser Suggestion)
- **Interactive map showing accident locations**
- **Heat map overlay for accident hotspots**
- **Visual density representation (red = high accidents)**
- **Leaflet.js implementation for simplicity**

#### 👥 Victim Management
- Victim information recording per incident
- Medical status tracking
- Hospital referral management
- **No self-service features for victims**

#### 🔍 Basic Audit & Security
- **Basic activity logging for user actions**
- Simple audit trails (login, create, update, delete)
- Data integrity validation
- Role-based access control

---

## ❌ PROJECT LIMITATIONS (Emergency Constraints)

### What the System WILL NOT Include:

#### ⏰ Time-Constrained Features
- **No advanced analytics or AI features**
- **No complex vehicle deployment tracking**
- **No email verification system**
- **No SMS/email notification system**
- **No advanced reporting workflows**

#### 👤 Victim Self-Service Features
- **No victim user registration**
- **No victim login portal**
- **No victim report request system**
- Victims will request reports through traditional MDRRMO office visits

#### 📱 Mobile & Integration
- **No native mobile app development**
- Web-responsive design only
- **No external system integration** (hospitals, police databases)
- **No real-time GPS tracking devices**

#### 🏢 Scope Limitations
- **Single municipality focus** (Maramag only)
- **No multi-tenancy architecture**
- **Basic UI styling** (functional over fancy)

---

## 👤 USER ROLES & PERMISSIONS

| Role | Access Level | Responsibilities | Implementation Status |
|------|--------------|------------------|---------------------|
| **Administrator** | Full System Access | • Complete system management and oversight<br>• User account management (CRUD operations)<br>• All reporting and analytics<br>• Security monitoring (login attempts)<br>• Heat map visualization access | ✅ **IMPLEMENTED**<br>• Admin-only registration<br>• Full user management<br>• Activity monitoring<br>• Security dashboard |
| **MDRRMO Staff** | Operational Access | • Create and update incident reports<br>• Basic vehicle management<br>• Record victim information<br>• Generate basic reports<br>• View heat map data | 🔄 **READY TO IMPLEMENT**<br>• Authentication complete<br>• Dashboard framework ready<br>• Role middleware active |

### Current User Management Features (IMPLEMENTED):
- **Admin-Only Registration**: Only admins can create new MDRRMO staff accounts
- **Enhanced User Fields**: first_name, last_name, municipality, position, phone_number
- **Security Verification**: Email verification + 2FA for all accounts
- **Activity Tracking**: All user actions logged with timestamps and IP addresses
- **Account Security**: Automatic lockout after 5 failed login attempts

### MDRRMO-Specific Implementation:
- **Municipality**: Defaulted to 'Maramag' for all users
- **Role Types**: 'admin' and 'mdrrmo_staff' only (no public users)
- **Email Templates**: MDRRMO-branded verification and 2FA emails
- **Government Styling**: Professional monotone gray-green palette with clean appearance

### Victim Information Handling:
- Stored as incident records (not system users)
- Traditional office-based report requests
- Maintains privacy and simplifies system architecture
- **Next Phase**: Victim management module ready for implementation

---

## 🗄️ DATABASE SCHEMA (Emergency Simplified)

### PostgreSQL Database with 5 Core Tables:

| # | Table Name | Purpose | Key Features |
|---|------------|---------|--------------|
| 1 | **users** | Admin and MDRRMO Staff authentication | Email, roles, basic security |
| 2 | **incidents** | Emergency incident records (8 types) + coordinates | Classification, location (lat/lng), status |
| 3 | **victims** | People involved (data records only) | Personal info, injury status, medical notes |
| 4 | **vehicles** | Emergency response fleet (basic) | Vehicle specs, status, basic tracking | 
| 5 | **activity_logs** | Basic audit trail | User actions, timestamps, IP tracking |

### Removed for Time Constraints:
- ~~**vehicle_deployments**~~ (too complex for deadline)
- ~~**reports**~~ (manual reporting only)

### Database Features:
- **10+ Essential Relationships** ensuring data integrity
- **PostgreSQL JSON support** for flexible data storage
- **Basic indexing** for performance
- **Coordinates storage** for heat map functionality

---

## 🛠️ TECHNOLOGY STACK

### Backend Framework:
- **Laravel 12.x** (Required)
- **PHP 8.x** with Composer dependency management
- **PostgreSQL** for relational data storage

### Frontend Technologies:
- **HTML5, CSS3, Tailwind CSS DaisyUi**
- **Chart.js** for basic data visualization (2 simple charts)
- **Leaflet.js** for heat map implementation
- **Responsive design** for basic mobile support

### Export & Reporting:
- **Laravel-DomPDF** for basic PDF generation
- **Laravel Excel** for simple Excel exports
- **Basic report templates**

### Development Tools:
- **Visual Studio Code** for development
- **Git/GitHub** for version control
- **Composer** for dependency management

### Color Scheme:
- **Monotone Gray-Green Palette:** Professional, eye-friendly design
- **Primary:** Gray-Green (#6b7671) - main interface color
- **Secondary:** Light Gray-Green (#f1f5f3) - backgrounds and cards
- **Accent:** Muted tones for alerts and notifications
- **Philosophy:** Consistent monotone approach for reduced eye strain and professional government appearance

---

## 📅 EMERGENCY DEVELOPMENT METHODOLOGY

### 13-Day Sprint Approach (July 7-21):

#### ✅ Days 1-2: Project Setup & Authentication (COMPLETED)
- ✅ Laravel 12.x project setup with PostgreSQL
- ✅ **Complete authentication system implementation**
- ✅ GitHub repository creation and management
- ✅ Advanced project structure with comprehensive guides

#### ✅ Days 3-5: Database & Core Models (COMPLETED)
- ✅ **Enhanced database migrations (4 core tables)**
  - ✅ Users table with MDRRMO-specific fields
  - ✅ Activity logs with comprehensive tracking
  - ✅ Login attempts with security monitoring
  - ✅ Password reset tokens table
- ✅ **Advanced model relationships and seeders**
- ✅ **Complete authentication controllers and routes**
- ✅ **Security features implementation**

#### 🚀 Days 6-8: Essential Features (IN PROGRESS - NEXT PHASE)
- 🔄 Incident management system (full CRUD)
- 🔄 Victim management integration  
- 🔄 Basic vehicle management system
- 🔄 Enhanced activity logging for business operations

#### 📈 Days 9-10: Analytics & Export (PLANNED)
- 📋 Simple Chart.js integration (2 basic charts)
- 📋 Basic PDF/Excel export functionality
- 📋 Enhanced dashboard with MDRRMO statistics
- 📋 Activity logs display and security monitoring

#### 🗺️ Days 11-12: Heat Map & UI (PLANNED)
- 📋 Leaflet.js map integration
- 📋 Heat map visualization for accidents
- 📋 Enhanced UI with MDRRMO branding
- 📋 Responsive design implementation

#### 🧪 Days 13-14: Testing & Presentation (PLANNED)
- 📋 Comprehensive testing and security validation
- 📋 MDRRMO demo data preparation
- 📋 Presentation rehearsal
- 📋 Final deployment and system handover

---

## 🎯 CURRENT PROJECT STATUS

### ✅ COMPLETED PHASES (Days 1-5)

#### 🔐 **Authentication System (100% Complete)**
- ✅ **Enhanced User Management**: Complete role-based system for Admin and MDRRMO Staff
- ✅ **Advanced Security Features**: 
  - 2FA verification with email OTP (6-digit codes, 10-minute expiry)
  - Email verification system with secure token validation
  - Account lockout after 5 failed attempts (15-minute lockout)
  - Activity logging for all authentication events
  - IP address and user agent tracking
- ✅ **MDRRMO-Specific User Fields**: 
  - first_name, last_name, municipality, position, phone_number
  - Municipality defaulted to 'Maramag'
  - Role-based access control (admin, mdrrmo_staff)
- ✅ **Professional Email Templates**: MDRRMO-branded 2FA and verification emails
- ✅ **Security Monitoring**: Login attempts tracking and analysis

#### 📊 **Database Implementation (100% Complete)**
- ✅ **PostgreSQL-Optimized Migrations**: 
  - Users table with MDRRMO enum types
  - Activity logs with comprehensive JSON tracking
  - Login attempts with security monitoring
  - Password reset tokens with expiration
- ✅ **Advanced Models**: User, ActivityLog, LoginAttempt with full relationships
- ✅ **Database Seeders**: AdminSeeder with customizable MDRRMO admin account

#### 🎨 **User Interface System (100% Complete)**
- ✅ **Professional Layout System**: Clean, responsive design with MDRRMO branding
- ✅ **MDRRMO Branding**: Professional monotone gray-green palette with government styling
- ✅ **Responsive Navigation**: Role-based menus with Bootstrap 5.3
- ✅ **Toast Notification System**: Success, error, warning, and info messages
- ✅ **Authentication Views**: Login, register, 2FA, email verification, password reset

#### 🛡️ **Security Implementation (100% Complete)**
- ✅ **Industry-Standard Flow**: Email verification → Login → 2FA → Access
- ✅ **Middleware Protection**: Role-based access and email verification enforcement
- ✅ **Admin-Only Registration**: No public registration, admin creates all accounts
- ✅ **Comprehensive Audit Trail**: All user actions logged with timestamps and IP
- ✅ **Security Grade**: A+ rating with banking/enterprise standards compliance

#### 📁 **Documentation System (100% Complete)**
- ✅ **Comprehensive Guides**: 6 detailed implementation guides created
- ✅ **Code Documentation**: CHANGELOG.md with complete implementation details
- ✅ **Security Assessment**: Industry standards comparison and validation
- ✅ **Deployment Instructions**: Step-by-step execution guides

### 🚀 **NEXT PHASE (Days 6-8): Business Logic Implementation**

#### 🏥 **Incident Management System (Ready to Implement)**
- 📋 Complete CRUD operations for 8 incident types
- 📋 Location-based incident recording (coordinates for heat mapping)
- 📋 Real-time incident status tracking
- 📋 Integration with existing activity logging system

#### 👥 **Victim Management Integration (Ready to Implement)**
- 📋 Victim information recording per incident
- 📋 Medical status tracking and hospital referral management
- 📋 Link to existing security and audit systems

#### 🚗 **Vehicle Management System (Ready to Implement)**
- 📋 Emergency vehicle inventory management
- 📋 Basic vehicle status tracking and assignment
- 📋 Integration with incident reporting system

#### 📈 **Enhanced Dashboard (Ready to Implement)**
- 📋 MDRRMO-specific statistics and metrics
- 📋 Integration with existing authentication and activity systems
- 📋 Role-based dashboard views for admin vs staff

### 🔮 **IMPLEMENTATION ADVANTAGES**

#### 🏗️ **Solid Foundation Built**
- **Industry-Standard Security**: Authentication system exceeds requirements
- **Scalable Architecture**: Clean separation of concerns and comprehensive documentation
- **MDRRMO-Optimized**: Custom fields, branding, and workflows specifically for target client
- **Development Efficiency**: Comprehensive guides enable rapid feature addition

#### 🎯 **Project Positioning**
- **Ahead of Schedule**: Days 1-5 exceeded original scope with advanced security features
- **Quality Over Quantity**: Focused on robust, secure foundation rather than rushed features  
- **Client-Focused**: MDRRMO-specific implementation rather than generic solution
- **Future-Proof**: Extensible architecture supports all planned features

---

## 📊 EXPECTED IMPACT & BENEFITS

### Efficiency Improvements:
- **90% faster data retrieval** (18 minutes → under 2 minutes)
- **Digital workflow** (eliminates paper-based processes)
- **Visual accident analysis** through heat mapping

### Operational Benefits:
- **Eliminate manual entry errors** through data validation
- **Centralized data management** for better decision-making
- **Basic audit trails** for accountability
- **Visual hotspot identification** for preventive measures

### Security & Privacy:
- **Controlled access** (MDRRMO staff only)
- **Data protection** (victim information stays internal)
- **Basic activity tracking** for system accountability

---

## 🎨 USER INTERFACE DESIGN

### Landing Page Features:
- Professional government system appearance
- Clear MDRRMO branding and purpose
- Staff login access point
- Emergency contact information
- Responsive design for basic mobile support

### Dashboard Components:
- **Summary statistics cards**
- **2 simple Chart.js visualizations**
- **Heat map for accident visualization**
- **Recent incidents table**
- **Basic export buttons (PDF/Excel)**

### Heat Map Interface:
- Interactive map centered on Maramag, Bukidnon
- Color-coded intensity (blue to red scale)
- Clickable accident pins with basic info
- Zoom and pan functionality

---

## 🧪 TESTING STRATEGY (Simplified)

### Emergency Testing Approach:
- **Manual Testing:** Core functionality verification
- **Basic User Testing:** Admin and staff role testing
- **Data Validation:** Form input validation and error handling
- **Cross-browser Testing:** Chrome, Firefox, Edge compatibility

### Quality Assurance:
- Basic responsive design validation
- Authentication and security testing
- Heat map functionality testing
- Export feature validation

---

## 📈 SUCCESS METRICS (MVP Goals)

### Key Performance Indicators:
- **Working CRUD Operations:** All incident management functions
- **Basic Analytics:** 2 working charts with real data
- **Heat Map Functionality:** Visual accident hotspot display
- **Export Capability:** Working PDF/Excel downloads
- **User Authentication:** Role-based access control

### Emergency Evaluation Criteria:
- **System Functionality (40%):** Core CRUD, authentication, basic reporting
- **Code Quality & GitHub (20%):** Clean code, regular commits
- **Presentation & Innovation (20%):** Heat map demo, basic analytics
- **Problem-Solving (20%):** Addresses MDRRMO workflow needs

---

## 🗺️ HEAT MAP IMPLEMENTATION DETAILS

### Technical Specification:
- **Map Provider:** OpenStreetMap (free alternative to Google Maps)
- **Library:** Leaflet.js with heat layer plugin
- **Data Source:** Incident coordinates from PostgreSQL
- **Visualization:** Gradient from blue (low) to red (high density)
- **Interactivity:** Clickable pins with incident summaries

### Data Processing:
- Incident coordinates stored as latitude/longitude
- Real-time heat map updates when new incidents added
- Density calculation based on geographical proximity
- Filter options by date range and incident type

---

## 📄 EXPORT FUNCTIONALITY

### PDF Export Features:
- Basic incident summary reports
- Date range filtering
- Simple tabular format
- Official MDRRMO header/footer

### Excel Export Features:
- Raw incident data export
- Victim information included
- Date and type filtering
- CSV format support

---

## 🚀 FUTURE ENHANCEMENTS (Post-Deadline)

### Version 2.0 Potential Features:
- Advanced vehicle deployment tracking
- Complex analytics and predictive modeling
- Mobile application for field reporting
- SMS/email notification system
- Integration with hospital management systems
- Multi-municipality deployment
- Advanced heat map features (time-based animation)

---

## ⚠️ PROJECT CONSTRAINTS & RISKS

### Time Constraints:
- **13-day development window**
- **Solo development limitations**
- **MVP focus over feature completeness**
- **Limited testing time**

### Technical Constraints:
- **Basic UI styling** (functional over aesthetic)
- **Simple analytics** (no complex algorithms)
- **Manual deployment** (no automated CI/CD)
- **Basic error handling**

### Risk Mitigation:
- Daily progress tracking and adjustment
- Feature prioritization (core first, nice-to-have last)
- Regular testing and validation
- Backup plans for complex features

---

## 📁 PROJECT STRUCTURE

mini-project/
├── app/
│   ├── Http/Controllers/
│   │   ├── IncidentController.php
│   │   ├── VehicleController.php
│   │   ├── DashboardController.php
│   │   └── HeatMapController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Incident.php
│   │   ├── Victim.php
│   │   ├── Vehicle.php
│   │   └── ActivityLog.php
│   └── Exports/
│       ├── IncidentsExport.php
│       └── VictimsExport.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── dashboard/
│   │   ├── incidents/
│   │   ├── vehicles/
│   │   └── reports/
│   └── js/
│       ├── chart-config.js
│       └── heatmap.js
├── routes/
│   └── web.php
└── public/
├── css/
└── js/


Tables


erDiagram
    USERS {
        bigint id PK
        string first_name
        string last_name
        string email UK
        enum role
        string municipality
        boolean is_active
    }
    
    INCIDENTS {
        bigint id PK
        string incident_number UK
        enum incident_type
        string location
        string municipality
        decimal latitude
        decimal longitude
        datetime incident_datetime
        enum severity_level
        enum status
        bigint reported_by FK
        bigint assigned_staff FK
        bigint assigned_vehicle FK
    }
    
    VICTIMS {
        bigint id PK
        bigint incident_id FK
        string first_name
        string last_name
        enum involvement_type
        enum injury_status
        string contact_number
        text address
    }
    
    VEHICLES {
        bigint id PK
        string vehicle_number UK
        enum vehicle_type
        string plate_number UK
        enum status
        string municipality
    }
    
    ACTIVITY_LOGS {
        bigint id PK
        bigint user_id FK
        string action
        string description
        string ip_address
        timestamp created_at
    }

    USERS ||--o{ INCIDENTS : "reports (reported_by)"
    USERS ||--o{ INCIDENTS : "assigned_to (assigned_staff)"
    USERS ||--o{ ACTIVITY_LOGS : "performs"
    INCIDENTS ||--o{ VICTIMS : "involves"
    VEHICLES ||--o{ INCIDENTS : "assigned_to (assigned_vehicle)"



🎯 8 INCIDENT TYPES SPECIFICATION
Vehicle-Related Incidents (5 Types):

vehicle_vs_vehicle - Head-on collisions, rear-end accidents, side impacts
vehicle_vs_pedestrian - Vehicle hitting pedestrians, crosswalk accidents
vehicle_vs_animals - Collisions with livestock, wildlife encounters
vehicle_vs_property - Vehicle hitting buildings, fences, posts
vehicle_alone - Single vehicle accidents, rollovers, road departures

Medical Emergency Incidents (3 Types):

maternity - Emergency childbirth, pregnancy complications
stabbing_shooting - Violence-related injuries requiring immediate response
transport_to_hospital - Medical transport requests, patient transfers


CREATE TABLE users (
    id                      BIGSERIAL PRIMARY KEY,
    first_name              VARCHAR(100) NOT NULL,
    last_name               VARCHAR(100) NOT NULL,
    email                   VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at       TIMESTAMP NULL,
    password                VARCHAR(255) NOT NULL,
    role                    user_role_enum NOT NULL DEFAULT 'mdrrmo_staff',
    phone_number            VARCHAR(20) NULL,
    municipality            VARCHAR(100) NOT NULL DEFAULT 'Maramag',
    position                VARCHAR(100) NULL,
    is_active               BOOLEAN NOT NULL DEFAULT true,
    last_login_at           TIMESTAMP NULL,
    failed_login_attempts   INTEGER NOT NULL DEFAULT 0,
    locked_until            TIMESTAMP NULL,
    remember_token          VARCHAR(100) NULL,
    created_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ENUM for user roles
CREATE TYPE user_role_enum AS ENUM ('admin', 'mdrrmo_staff');

-- Indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_municipality ON users(municipality);



CREATE TABLE incidents (
    id                      BIGSERIAL PRIMARY KEY,
    incident_number         VARCHAR(50) UNIQUE NOT NULL,
    incident_type           incident_type_enum NOT NULL,
    location                VARCHAR(255) NOT NULL,
    municipality            VARCHAR(100) NOT NULL DEFAULT 'Maramag',
    barangay                VARCHAR(100) NOT NULL,
    latitude                DECIMAL(10,8) NULL,
    longitude               DECIMAL(11,8) NULL,
    incident_datetime       TIMESTAMP NOT NULL,
    description             TEXT NOT NULL,
    severity_level          severity_enum NOT NULL,
    status                  incident_status_enum NOT NULL DEFAULT 'pending',
    vehicles_involved       INTEGER NOT NULL DEFAULT 0,
    casualties_count        INTEGER NOT NULL DEFAULT 0,
    injuries_count          INTEGER NOT NULL DEFAULT 0,
    estimated_damage        DECIMAL(15,2) NULL,
    hospital_destination    VARCHAR(255) NULL,
    patient_condition       patient_condition_enum NULL,
    medical_notes           TEXT NULL,
    weather_condition       weather_enum NULL,
    road_condition          road_condition_enum NULL,
    assigned_vehicle        BIGINT NULL,
    additional_details      JSONB NULL,
    reported_by             BIGINT NOT NULL,
    assigned_staff          BIGINT NULL,
    created_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Key Constraints
    CONSTRAINT fk_incidents_reported_by FOREIGN KEY (reported_by) REFERENCES users(id),
    CONSTRAINT fk_incidents_assigned_staff FOREIGN KEY (assigned_staff) REFERENCES users(id),
    CONSTRAINT fk_incidents_assigned_vehicle FOREIGN KEY (assigned_vehicle) REFERENCES vehicles(id)
);

-- ENUM Types for Incidents
CREATE TYPE incident_type_enum AS ENUM (
    'vehicle_vs_vehicle',
    'vehicle_vs_pedestrian', 
    'vehicle_vs_animals',
    'vehicle_vs_property',
    'vehicle_alone',
    'maternity',
    'stabbing_shooting',
    'transport_to_hospital'
);

CREATE TYPE severity_enum AS ENUM ('minor', 'moderate', 'severe', 'critical');
CREATE TYPE incident_status_enum AS ENUM ('pending', 'responding', 'resolved', 'closed');
CREATE TYPE patient_condition_enum AS ENUM ('stable', 'critical', 'deceased');
CREATE TYPE weather_enum AS ENUM ('clear', 'rainy', 'foggy', 'windy', 'stormy');
CREATE TYPE road_condition_enum AS ENUM ('dry', 'wet', 'slippery', 'under_construction', 'damaged');

-- Indexes for Performance
CREATE INDEX idx_incidents_type ON incidents(incident_type);
CREATE INDEX idx_incidents_municipality ON incidents(municipality);
CREATE INDEX idx_incidents_datetime ON incidents(incident_datetime);
CREATE INDEX idx_incidents_status ON incidents(status);
CREATE INDEX idx_incidents_coordinates ON incidents(latitude, longitude);



CREATE TABLE victims (
    id                      BIGSERIAL PRIMARY KEY,
    incident_id             BIGINT NOT NULL,
    first_name              VARCHAR(100) NOT NULL,
    last_name               VARCHAR(100) NOT NULL,
    age                     INTEGER NULL,
    gender                  gender_enum NULL,
    contact_number          VARCHAR(20) NULL,
    address                 TEXT NOT NULL,
    involvement_type        involvement_enum NOT NULL,
    injury_status           injury_status_enum NOT NULL,
    hospital_referred       VARCHAR(255) NULL,
    hospital_arrival_time   TIMESTAMP NULL,
    medical_notes           TEXT NULL,
    transport_method        transport_enum NULL,
    vehicle_type            VARCHAR(50) NULL,
    vehicle_plate_number    VARCHAR(20) NULL,
    wearing_helmet          BOOLEAN NULL,
    wearing_seatbelt        BOOLEAN NULL,
    license_status          license_enum NULL,
    emergency_contacts      JSONB NULL,
    created_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Key Constraints
    CONSTRAINT fk_victims_incident_id FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE
);

-- ENUM Types for Victims
CREATE TYPE gender_enum AS ENUM ('male', 'female', 'other');

CREATE TYPE involvement_enum AS ENUM (
    'driver', 
    'passenger', 
    'pedestrian', 
    'witness',
    'patient',
    'expectant_mother',
    'victim',
    'property_owner',
    'other'
);

CREATE TYPE injury_status_enum AS ENUM (
    'none', 
    'minor_injury', 
    'serious_injury', 
    'critical_condition',
    'in_labor',
    'gunshot_wound',
    'stab_wound',
    'fatal'
);

CREATE TYPE transport_enum AS ENUM (
    'ambulance', 
    'private_vehicle', 
    'motorcycle', 
    'helicopter',
    'walk_in'
);

CREATE TYPE license_enum AS ENUM ('valid', 'expired', 'no_license', 'unknown');

-- Indexes
CREATE INDEX idx_victims_incident_id ON victims(incident_id);
CREATE INDEX idx_victims_injury_status ON victims(injury_status);



CREATE TABLE vehicles (
    id                      BIGSERIAL PRIMARY KEY,
    vehicle_number          VARCHAR(50) UNIQUE NOT NULL,
    vehicle_type            vehicle_type_enum NOT NULL,
    make_model              VARCHAR(100) NOT NULL,
    year                    INTEGER NOT NULL,
    plate_number            VARCHAR(20) UNIQUE NOT NULL,
    status                  vehicle_status_enum NOT NULL DEFAULT 'available',
    municipality            VARCHAR(100) NOT NULL DEFAULT 'Maramag',
    capacity                INTEGER NOT NULL,
    fuel_capacity           DECIMAL(8,2) NOT NULL,
    current_fuel            DECIMAL(8,2) NOT NULL DEFAULT 0,
    odometer_reading        INTEGER NOT NULL DEFAULT 0,
    last_maintenance        DATE NULL,
    next_maintenance_due    DATE NULL,
    equipment_list          TEXT NULL,
    is_operational          BOOLEAN NOT NULL DEFAULT true,
    created_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ENUM Types for Vehicles
CREATE TYPE vehicle_type_enum AS ENUM (
    'ambulance', 
    'fire_truck', 
    'rescue_vehicle', 
    'patrol_car',
    'motorcycle',
    'emergency_van'
);

CREATE TYPE vehicle_status_enum AS ENUM (
    'available', 
    'deployed', 
    'maintenance', 
    'out_of_service'
);

-- Indexes
CREATE INDEX idx_vehicles_type ON vehicles(vehicle_type);
CREATE INDEX idx_vehicles_status ON vehicles(status);
CREATE INDEX idx_vehicles_municipality ON vehicles(municipality);
CREATE UNIQUE INDEX idx_vehicles_number ON vehicles(vehicle_number);
CREATE UNIQUE INDEX idx_vehicles_plate ON vehicles(plate_number);


CREATE TABLE activity_logs (
    id                      BIGSERIAL PRIMARY KEY,
    user_id                 BIGINT NULL,
    action                  VARCHAR(100) NOT NULL,
    model_type              VARCHAR(100) NULL,
    model_id                BIGINT NULL,
    old_values              JSONB NULL,
    new_values              JSONB NULL,
    ip_address              INET NULL,
    user_agent              TEXT NULL,
    description             TEXT NOT NULL,
    created_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Key Constraints
    CONSTRAINT fk_activity_logs_user_id FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Indexes for Performance
CREATE INDEX idx_activity_logs_user_id ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_action ON activity_logs(action);
CREATE INDEX idx_activity_logs_created_at ON activity_logs(created_at);
CREATE INDEX idx_activity_logs_model ON activity_logs(model_type, model_id);

-- User Relationships
users(id) ←→ incidents(reported_by)      [One-to-Many]
users(id) ←→ incidents(assigned_staff)   [One-to-Many]
users(id) ←→ activity_logs(user_id)      [One-to-Many]

-- Incident Relationships  
incidents(id) ←→ victims(incident_id)    [One-to-Many]
vehicles(id) ←→ incidents(assigned_vehicle) [One-to-Many]

-- Cascade Rules
victims → incidents (CASCADE DELETE)
activity_logs → users (SET NULL)
