function showMessage(message, type, targetDivId = "editMessage") {
  const messageDiv = document.getElementById(targetDivId);
  if (messageDiv) {
    if (message) {
      messageDiv.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    } else {
      messageDiv.innerHTML = "";
    }
  }
}
const addPostForm = document.getElementById("add-post-form");
if (addPostForm) {
  addPostForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const messageDiv = document.getElementById("form-message");

    if (messageDiv) messageDiv.innerHTML = "";

    try {
      const response = await fetch(window.location.href, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          topic_id: formData.get("topic_id"),
          title: formData.get("title"),
          content: formData.get("content"),
        }),
      });

      const result = await response.json();

      if (result.status === "success") {
        window.location.href = result.redirect;
      } else {
        if (messageDiv) {
          showMessage(result.message, "danger", "form-message");
        }
      }
    } catch (error) {
      if (messageDiv) {
        showMessage(
          "Сталася помилка. Спробуйте ще раз.",
          "danger",
          "form-message"
        );
      }
      console.error("Помилка при додаванні поста:", error);
    }
  });
}

const editPostBtn = document.getElementById("editPostBtn");
if (editPostBtn) {
  editPostBtn.addEventListener("click", () => {
    const postTitleEl = document.getElementById("postTitle");
    const postContentEl = document.getElementById("postContent");

    if (!postTitleEl || !postContentEl) return;

    const originalTitle = postTitleEl.textContent.trim();
    const originalContent = postContentEl.textContent.trim();

    const titleInput = document.createElement("input");
    titleInput.type = "text";
    titleInput.id = "editPostTitle";
    titleInput.className = "form-control mb-3";
    titleInput.value = originalTitle;

    const contentTextarea = document.createElement("textarea");
    contentTextarea.id = "editPostContent";
    contentTextarea.className = "form-control mb-3";
    contentTextarea.rows = 6;
    contentTextarea.value = originalContent;

    const saveBtn = document.createElement("button");
    saveBtn.className = "btn btn-success me-2";
    saveBtn.textContent = "Зберегти";

    const cancelBtn = document.createElement("button");
    cancelBtn.className = "btn btn-secondary";
    cancelBtn.textContent = "Скасувати";

    const messageDiv = document.createElement("div");
    messageDiv.id = "postEditMessage";
    messageDiv.className = "mt-2";

    postTitleEl.replaceWith(titleInput);
    postContentEl.replaceWith(contentTextarea);
    editPostBtn.style.display = "none";

    contentTextarea.parentElement.appendChild(saveBtn);
    contentTextarea.parentElement.appendChild(cancelBtn);
    contentTextarea.parentElement.appendChild(messageDiv);

    cancelBtn.addEventListener("click", () => {
      const restoredTitleEl = document.createElement("h2");
      restoredTitleEl.id = "postTitle";
      restoredTitleEl.className = "mb-0";
      restoredTitleEl.textContent = originalTitle;

      const restoredContentEl = document.createElement("p");
      restoredContentEl.id = "postContent";
      restoredContentEl.className = "card-text";
      restoredContentEl.innerHTML = originalContent.replace(/\n/g, "<br>");

      titleInput.replaceWith(restoredTitleEl);
      contentTextarea.replaceWith(restoredContentEl);
      saveBtn.remove();
      cancelBtn.remove();
      messageDiv.remove();
      editPostBtn.style.display = "inline-block";
    });

    saveBtn.addEventListener("click", async () => {
      const newTitle = titleInput.value.trim();
      const newContent = contentTextarea.value.trim();
      const postId = editPostBtn.dataset.postId;

      if (!newTitle || !newContent) {
        showMessage(
          "Заголовок не може бути порожнім.",
          "danger",
          "postEditMessage"
        );
        return;
      }
      if (!newContent) {
        showMessage(
          "Вміст не може бути порожнім.",
          "danger",
          "postEditMessage"
        );
        return;
      }
      try {
        const response = await fetch(`/posts/edit/${postId}`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({ title: newTitle, content: newContent }),
        });
        const result = await response.json();

        if (result.status === "success") {
          const updatedTitleEl = document.createElement("h2");
          updatedTitleEl.id = "postTitle";
          updatedTitleEl.className = "mb-0";
          updatedTitleEl.textContent = newTitle;

          const updatedContentEl = document.createElement("p");
          updatedContentEl.id = "postContent";
          updatedContentEl.className = "card-text";
          updatedContentEl.innerHTML = newContent.replace(/\n/g, "<br>");

          titleInput.replaceWith(updatedTitleEl);
          contentTextarea.replaceWith(updatedContentEl);
          saveBtn.remove();
          cancelBtn.remove();
          editPostBtn.style.display = "inline-block";
        } else {
          showMessage(result.message, "danger", "postEditMessage");
        }
      } catch (err) {
        showMessage(
          "Сталася помилка при збереженні. Спробуйте ще раз.",
          "danger",
          "postEditMessage"
        );
      }
    });
  });
}
