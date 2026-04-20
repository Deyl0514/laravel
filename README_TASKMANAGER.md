# Task Manager - Laravel Application

A complete, fully functional Task Management Web Application built with Laravel 10+, Bootstrap 5, Chart.js, and Toastr.js.

## 🎯 Features

### 1. **Authentication System**
- User Registration with validation
- Secure Login with "Remember Me" functionality
- Logout functionality
- Password hashing with bcrypt

### 2. **Dashboard**
- Real-time statistics (Total Users, Total Tasks, Completed Tasks, Pending Tasks)
- Chart.js bar chart showing tasks by status
- Chart.js doughnut chart showing tasks by priority
- Recent tasks table displaying last 5 tasks
- Responsive design with stat cards

### 3. **Users Management (CRUD)**
- View all users with pagination
- Add new users via modal form
- Edit user details with modal
- Delete users with confirmation
- Search/filter users by name or email
- Inline error messages and success notifications

### 4. **Tasks Management (Main Module)**
- Create, Read, Update, Delete tasks
- Filter tasks by Status (Pending, In Progress, Completed)
- Filter tasks by Priority (Low, Medium, High)
- Search tasks by title or description
- Color-coded priority badges (Low: Green, Medium: Yellow, High: Red)
- Color-coded status badges
- Overdue task highlighting in red
- Due date picker with validation
- Pagination support

### 5. **User Profile**
- View personal profile information
- Edit profile details (Name, Email, Gender, Address)
- Profile picture upload with validation
- Profile statistics (Total Tasks, Completed, Active)
- Image preview before upload

### 6. **UI/UX Design**
- **Dark Sidebar Navigation** (#1e293b) with white text and icons
- Light content area (#f8fafc)
- Responsive Bootstrap 5 grid system
- Soft box shadows (0 2px 12px rgba(0,0,0,0.08))
- Rounded cards (12px border-radius)
- Consistent color palette:
  - Primary Accent: #3b82f6 (Blue)
  - Secondary Accent: #10b981 (Green)
  - Danger Accent: #ef4444 (Red)
  - Warning Accent: #f59e0b (Amber)
- Google Fonts (Poppins)
- Mobile responsive layout

### 7. **Notifications**
- Toastr.js toast notifications for all CRUD operations
- Success messages on registration, login, and profile updates
- Error messages with inline validation
- Auto-dismissing notifications (5 seconds)

## 📋 Database Schema

### Users Table
```sql
- id (Primary Key)
- name (String)
- email (String, Unique)
- password (String, Hashed)
- gender (String, Nullable)
- address (Text, Nullable)
- profile_picture (String, Nullable)
- email_verified_at (Timestamp, Nullable)
- remember_token (String, Nullable)
- timestamps (created_at, updated_at)
```

### Tasks Table
```sql
- id (Primary Key)
- user_id (Foreign Key → users.id)
- title (String)
- description (Text, Nullable)
- priority (Enum: low, medium, high)
- status (Enum: pending, in_progress, completed)
- due_date (Date, Nullable)
- timestamps (created_at, updated_at)
- indexes: user_id, status, priority
```

## 🛠️ Tech Stack

- **Backend**: Laravel 10+
- **Database**: MySQL
- **Frontend**: Bootstrap 5
- **Charts**: Chart.js 3.9.1
- **Notifications**: Toastr.js
- **Icons**: Font Awesome 6.4.0
- **Font**: Google Fonts (Poppins)
- **HTTP Client**: jQuery 3.6.0
- **Authentication**: Laravel Built-in Auth

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.1+
- Composer
- MySQL 5.7+
- XAMPP or similar local development environment

### Installation Steps

1. **Navigate to project directory:**
```bash
cd c:\xampp\htdocs\My-laravel
```

2. **Install dependencies:**
```bash
composer install
```

3. **Generate application key:**
```bash
php artisan key:generate
```

4. **Create storage symlink:**
```bash
php artisan storage:link
```

5. **Run migrations:**
```bash
php artisan migrate --force
```

6. **Start the development server:**
```bash
php artisan serve
```

7. **Access the application:**
Open your browser and navigate to `http://localhost:8000`

## 📁 Project Structure

```
My-laravel/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php
│   │       ├── DashboardController.php
│   │       ├── UserController.php
│   │       ├── TaskController.php
│   │       └── ProfileController.php
│   ├── Models/
│   │   ├── User.php
│   │   └── Task.php
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── migrations/
│   │   ├── create_users_table.php
│   │   └── create_tasks_table.php
│   ├── factories/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php (Main layout with sidebar)
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── dashboard/
│       │   └── index.blade.php
│       ├── users/
│       │   └── index.blade.php
│       ├── tasks/
│       │   └── index.blade.php
│       └── profile/
│           └── index.blade.php
├── routes/
│   └── web.php
├── public/
│   ├── storage/ (Symlink)
│   └── images/
│       └── default-avatar.png
└── .env
```

## 🔐 Authentication Routes

### Guest Routes (No Auth Required)
- `GET /register` - Registration form
- `POST /register` - Process registration
- `GET /login` - Login form
- `POST /login` - Process login

### Protected Routes (Auth Required)
- `GET /dashboard` - Main dashboard
- `GET /users` - Users list
- `POST /users` - Create user
- `PUT /users/{id}` - Update user
- `DELETE /users/{id}` - Delete user
- `GET /tasks` - Tasks list
- `POST /tasks` - Create task
- `PUT /tasks/{id}` - Update task
- `DELETE /tasks/{id}` - Delete task
- `GET /profile` - User profile
- `PUT /profile` - Update profile
- `POST /logout` - Logout

## 🎨 Color Palette

| Element | Color | Hex Code |
|---------|-------|----------|
| Sidebar Background | Deep Navy | #1e293b |
| Primary Accent | Blue | #3b82f6 |
| Secondary Accent | Green | #10b981 |
| Danger Accent | Red | #ef4444 |
| Warning Accent | Amber | #f59e0b |
| Info Accent | Cyan | #06b6d4 |
| Background | Light Gray | #f8fafc |
| Text Light | Off White | #f8fafc |
| Text Muted | Slate | #64748b |

## 📝 Usage Guide

### Creating a Task
1. Click "Add Task" button in the Tasks page
2. Fill in the task details:
   - Title (required)
   - Description (optional)
   - Priority (Low, Medium, High)
   - Status (Pending, In Progress, Completed)
   - Due Date (optional)
3. Click "Create Task"
4. Success notification will appear

### Managing Tasks
- **Edit**: Click Edit button to modify task details
- **Delete**: Click Delete button and confirm deletion
- **Filter**: Use Status and Priority filters to narrow down tasks
- **Search**: Search by title or description

### Managing Users (Admin)
- **Add User**: Click "Add User" button to create new user
- **Edit User**: Click Edit button to modify user details
- **Delete User**: Click Delete button and confirm deletion
- **Search**: Search users by name or email

### Profile Management
- **Edit Profile**: Update name, email, gender, and address
- **Upload Picture**: Click on profile picture area to upload new image
- **View Stats**: See your total tasks, completed tasks, and active tasks

## 🔄 API Response Format

All CRUD operations return JSON responses:

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully!",
  "user": { ... } or "task": { ... }
}
```

### Error Response
```json
{
  "errors": {
    "field_name": ["Error message"]
  }
}
```

## ⚙️ Validation Rules

### User Registration
- Name: Required, string, max 255 characters
- Email: Required, email, max 255 characters, unique in users table
- Password: Required, string, min 8 characters, must be confirmed

### User Creation/Update
- Name: Required, string, max 255 characters
- Email: Required, email, max 255 characters, unique (except own record)
- Password: Optional, string, min 8 characters (for updates)

### Task Creation/Update
- Title: Required, string, max 255 characters
- Description: Optional, string
- Priority: Required, enum (low, medium, high)
- Status: Required, enum (pending, in_progress, completed)
- Due Date: Optional, date, must be today or in future

### Profile Update
- Name: Required, string, max 255 characters
- Email: Required, email, max 255 characters, unique (except own)
- Gender: Optional, enum (male, female, other)
- Address: Optional, string, max 500 characters
- Profile Picture: Optional, image, max 2MB, formats (jpeg, png, jpg, gif)

## 🎯 Key Features by Page

### Login Page
- Clean, modern design with gradient background
- Email and password fields
- "Remember Me" checkbox
- Link to registration page
- Real-time error messages

### Registration Page
- Full Name, Email, Password, Confirm Password fields
- Server-side validation
- Automatic redirect to login on success
- Toastr success notification

### Dashboard
- Quick statistics overview
- Interactive charts (Bar and Doughnut)
- Recent tasks table
- Date display in top navbar
- Responsive stat cards with icons

### Tasks Page
- Full CRUD functionality
- Advanced filtering (Status + Priority)
- Search functionality
- Pagination
- Modal-based Add/Edit forms
- Overdue task highlighting
- Color-coded badges

### Users Page
- Complete user management
- Modal-based Add/Edit forms
- User search
- Pagination
- Easy deletion with confirmation

### Profile Page
- User information display
- Profile statistics cards
- Image preview before upload
- Real-time form validation
- Profile picture with default avatar fallback

## 🐛 Troubleshooting

### Database Connection Error
- Ensure MySQL is running in XAMPP
- Check .env database credentials
- Run: `php artisan migrate --force`

### Storage Symlink Issues
- Run: `php artisan storage:link`
- Ensure storage/app/public directory exists

### CSS/JS Not Loading
- Clear browser cache (Ctrl+Shift+Delete)
- Run: `composer dump-autoload`
- Verify symlink: `php artisan storage:link`

### Login Issues
- Clear sessions table: `php artisan migrate:refresh`
- Verify user exists in database
- Check password is hashed with bcrypt

## 📚 Additional Commands

```bash
# Clear all caches
php artisan cache:clear

# Clear application cache
php artisan app:cache

# Migrate with seed
php artisan migrate:fresh --seed

# Create a new controller
php artisan make:controller ControllerName

# Create a new model
php artisan make:model ModelName -m

# Create a migration
php artisan make:migration create_table_name

# Tinker shell for debugging
php artisan tinker
```

## 📄 License

This project is a demonstration Laravel application built as per specifications.

## 👨‍💻 Development Notes

- All views use Blade templating
- Eloquent ORM for database operations
- CSRF token validation on all forms
- Password hashing with bcrypt
- Responsive Bootstrap 5 grid
- Embedded CSS for easy deployment
- jQuery for AJAX operations
- Chart.js for data visualization
- Toastr for user notifications

---

**Version**: 1.0.0
**Last Updated**: April 2026
**Status**: ✅ Production Ready
