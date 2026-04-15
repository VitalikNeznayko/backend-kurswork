# API Documentation (ForumFlow)

Base URL:
http://localhost/ForumFlow

---

## 1. Get Categories

### GET /categories

Description:
Returns all forum categories.

Response 200:
``` bash
[
  {
    "id": 1,
    "title": "Programming",
    "description": "All about coding"
  }
]
```
---

## 2. Create Post

### POST /posts/add

Description:
Creates a new post inside a topic.  
User must be authenticated.

Request body:
``` bash
{
  "topic_id": 1,
  "title": "Hello world",
  "content": "This is my first post"
}
```

Response 200:
``` bash
{
  "status": "success",
  "redirect": "/topics/view/1"
}
```
---

## ❌ Error Response
``` bash
{
  "status": "error",
  "message": "Оберіть тему"
}
```
