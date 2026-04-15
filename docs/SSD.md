# System Specification Document

## 1. System Overview
ForumFlow is a web-based forum application built using PHP MVC architecture and MySQL database.

The system allows users to browse categories, create topics, posts, and comments, and interact within structured discussions.

---

## 2. Technology Stack
- Backend: PHP (MVC architecture)
- Database: MySQL
- Frontend: HTML, CSS, JavaScript (AJAX)
- Server: Apache (XAMPP or similar)

---

## 3. MVC Architecture

### Models
- Users
- Categories
- Topics
- Posts
- Comments

---

### Controllers

The system contains the following controllers:

- AdminController  
- CategoryController  
- CommentsController  
- ErrorController  
- PostsController  
- SiteController  
- TopicsController  
- UsersController  

#### Controller Responsibilities:

- **AdminController**
  - Admin dashboard
  - Content moderation (users, posts, comments)

- **CategoryController**
  - Manage categories (create, list, view)

- **TopicsController**
  - Handle topics inside categories

- **PostsController**
  - Create, edit, delete posts

- **CommentsController**
  - Add and manage comments under posts

- **UsersController**
  - Registration, login, profile management

- **SiteController**
  - Main pages (home, landing page, general views)

- **ErrorController**
  - Handle system errors (404, 500, etc.)

---

### Views
- Category pages
- Topic pages
- Post pages
- Admin panel views
- Authentication pages

---

## 4. Database Schema

### users
- id (INT, PK)
- name (VARCHAR)
- email (VARCHAR)
- password (VARCHAR)
- isAdmin (BOOLEAN)

---

### categories
- id (INT, PK)
- title (VARCHAR)
- description (TEXT)
- created_at (DATETIME)

---

### topics
- id (INT, PK)
- category_id (INT, FK)
- title (VARCHAR)
- description (TEXT)
- created_at (DATETIME)

---

### posts
- id (INT, PK)
- topic_id (INT, FK)
- user_id (INT, FK)
- content (TEXT)
- created_at (DATETIME)

---

### comments
- id (INT, PK)
- post_id (INT, FK)
- user_id (INT, FK)
- content (TEXT)
- created_at (DATETIME)

---

## 5. System Features
- User authentication (register/login/logout)
- Category management
- Topic management inside categories
- Post creation and management
- Comment system
- Admin panel for moderation
- Error handling system

---

## 6. Security
- Password hashing (bcrypt / password_hash)
- Session-based authentication
- Role-based access control (user/admin)

---

## 7. Performance Considerations
- Indexed foreign keys
- Optimized relational queries
- Minimal controller coupling