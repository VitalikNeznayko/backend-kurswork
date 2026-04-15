# Business Requirements Document

## 1. Project Overview
This project is a web-based forum system built using PHP MVC architecture and MySQL database.  
The system allows users to create topics, publish posts, and interact through comments.

## 2. Business Goals
- Provide a platform for user discussions
- Allow users to create and manage posts
- Enable communication through comments
- Separate roles for users and administrators

## 3. Stakeholders
- End users (forum participants)
- Administrators (content moderation)
- System owner / developer

## 4. User Roles

### Guest
- View topics and posts
- Cannot create content

### Registered User
- Create posts
- Comment on posts
- View all content

### Administrator
- Manage users
- Delete/edit posts and comments
- Moderate content

## 5. Functional Requirements
- User registration and login system
- CRUD operations for posts
- CRUD operations for comments
- Topic-based organization of posts
- User authentication and authorization

## 6. Non-Functional Requirements
- Secure password storage (hashing)
- Session-based authentication
- Fast response time for requests
- Scalable database structure