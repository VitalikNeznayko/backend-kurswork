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

function buildCommentHtml(comment, currentUserData, isReply = false) {
  const marginLeft = isReply ? "30px" : "0";
  const marginTop = "15px";

  const editFormHtml =
    currentUserData && currentUserData.id === comment.user_id
      ? `
        <div class="edit-comment-form-container mt-2" style="display: none;">
            <textarea class="form-control edit-comment-textarea" rows="3">${comment.content}</textarea>
            <button class="btn btn-success btn-sm mt-2 save-comment-btn" data-comment-id="${comment.id}">Зберегти</button>
            <button class="btn btn-secondary btn-sm mt-2 cancel-edit-comment-btn">Скасувати</button>
            <div class="edit-comment-error-message text-danger mt-2"></div>
        </div>`
      : "";

  const replyFormHtml = currentUserData
    ? `
        <button type="button" class="btn btn-link btn-sm reply-comment-btn" data-comment-id="${comment.id}">
            Відповісти
        </button>
        <div class="reply-comment-form-container mt-2" style="display:none;">
            <textarea class="form-control reply-comment-textarea" rows="3" placeholder="Ваша відповідь..."></textarea>
            <button class="btn btn-primary btn-sm mt-2 submit-reply-btn" data-parent-id="${comment.id}">Відправити</button>
            <button class="btn btn-secondary btn-sm mt-2 cancel-reply-btn">Скасувати</button>
            <div class="reply-comment-error-message text-danger mt-2"></div>
        </div>`
    : "";

  const footerButtonsHtml =
    currentUserData && currentUserData.id === comment.user_id
      ? `
        <div class="card-footer text-end">
            <button type="button" class="btn btn-sm btn-outline-primary edit-comment-btn" data-comment-id="${comment.id}">
                Редагувати
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="${comment.id}">
                Видалити
            </button>
        </div>`
      : "";

  return `
        <div class="col" id="comment-${
          comment.id
        }" style="margin-left: ${marginLeft}; margin-top: ${marginTop}">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>${comment.authorName || "Гість"}</strong>
                    <small class="text-muted">${
                      comment.created_at_formatted
                    }</small>
                </div>
                <div class="card-body">
                    <p class="card-text comment-content">${comment.content.replace(
                      /\n/g,
                      "<br>"
                    )}</p>
                    ${editFormHtml}
                    ${replyFormHtml}
                </div>
                ${footerButtonsHtml}
            </div>
            <div class="replies-container"></div>
        </div>
    `;
}

function buildCommentsTreeHtml(comments, currentUserData, parentId = null) {
  let html = "";
  const filteredAndSortedComments = comments
    .filter((comment) => comment.parent_id === parentId)
    .sort((a, b) => new Date(a.created_at_raw) - new Date(b.created_at_raw));

  filteredAndSortedComments.forEach((comment) => {
    const isReply = parentId !== null;
    const commentHtml = buildCommentHtml(comment, currentUserData, isReply);

    const tempDiv = document.createElement("div");
    tempDiv.innerHTML = commentHtml;
    const currentCommentElement = tempDiv.firstElementChild;

    const repliesContainer =
      currentCommentElement.querySelector(".replies-container");
    if (repliesContainer) {
      const nestedRepliesHtml = buildCommentsTreeHtml(
        comments,
        currentUserData,
        comment.id
      );
      if (nestedRepliesHtml) {
        repliesContainer.innerHTML = nestedRepliesHtml;
      }
    }
    html += tempDiv.innerHTML;
  });
  return html;
}

const commentsList = document.getElementById("comments-list");
if (commentsList) {
  commentsList.addEventListener("click", async function (e) {
    if (e.target.classList.contains("edit-comment-btn")) {
      const commentId = e.target.dataset.commentId;
      const commentCard = e.target.closest(".col");
      const commentContentP = commentCard.querySelector(".comment-content");
      const editFormContainer = commentCard.querySelector(
        ".edit-comment-form-container"
      );
      const editCommentTextarea = editFormContainer.querySelector(
        ".edit-comment-textarea"
      );
      const replyButton = commentCard.querySelector(".reply-comment-btn");

      commentContentP.style.display = "none";
      editFormContainer.style.display = "block";
      e.target.style.display = "none";

      if (replyButton) {
        replyButton.style.display = "none";
      }

      const originalContent = commentContentP.textContent.trim();
      editCommentTextarea.value = originalContent;
    }

    if (e.target.classList.contains("save-comment-btn")) {
      const commentId = e.target.dataset.commentId;
      const commentCard = e.target.closest(".col");
      const commentContentP = commentCard.querySelector(".comment-content");
      const editFormContainer = commentCard.querySelector(
        ".edit-comment-form-container"
      );
      const editCommentTextarea = editFormContainer.querySelector(
        ".edit-comment-textarea"
      );
      const errorMessageDiv = editFormContainer.querySelector(
        ".edit-comment-error-message"
      );
      const editButton = commentCard.querySelector(".edit-comment-btn");
      const replyButton = commentCard.querySelector(".reply-comment-btn");

      const newContent = editCommentTextarea.value.trim();

      if (errorMessageDiv) errorMessageDiv.innerHTML = "";

      if (!newContent) {
        if (errorMessageDiv) {
          errorMessageDiv.innerHTML = `<div class="alert alert-danger">Вміст коментаря не може бути порожнім.</div>`;
        }
        return;
      }

      try {
        const response = await fetch(`/comments/edit/${commentId}`, {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ content: newContent }),
        });
        const result = await response.json();

        if (result.status === "success") {
          commentContentP.textContent = newContent;
          commentContentP.style.display = "block";
          editFormContainer.style.display = "none";
          if (editButton) editButton.style.display = "inline-block";
          if (replyButton) {
            replyButton.style.display = "inline-block";
          }
        } else {
          if (errorMessageDiv) {
            errorMessageDiv.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
          }
        }
      } catch (error) {
        if (errorMessageDiv) {
          errorMessageDiv.innerHTML = `<div class="alert alert-danger">Сталася помилка при збереженні коментаря. Спробуйте ще раз.</div>`;
        }
        console.error("Помилка при збереженні коментаря:", error);
      }
    }


    if (e.target.classList.contains("cancel-edit-comment-btn")) {
      const commentCard = e.target.closest(".col");
      const commentContentP = commentCard.querySelector(".comment-content");
      const editFormContainer = commentCard.querySelector(
        ".edit-comment-form-container"
      );
      const editButton = commentCard.querySelector(".edit-comment-btn");
      const errorMessageDiv = editFormContainer.querySelector(
        ".edit-comment-error-message"
      );
      const replyButton = commentCard.querySelector(".reply-comment-btn");

      commentContentP.style.display = "block";
      editFormContainer.style.display = "none";
      if (editButton) editButton.style.display = "inline-block";
      if (replyButton) {
        replyButton.style.display = "inline-block";
      }
      if (errorMessageDiv) errorMessageDiv.innerHTML = "";
    }

    if (e.target.classList.contains("delete-comment-btn")) {
      const commentId = e.target.dataset.commentId;

      try {
        const response = await fetch(`/comments/delete/${commentId}`, {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        });
        const result = await response.json();

        if (result.status === "success") {
          const commentElement = document.getElementById(
            `comment-${commentId}`
          );
          if (commentElement) {
            commentElement.remove(); 

            const commentsCountSpan = document.getElementById("comments-count");
            if (commentsCountSpan) {

              commentsCountSpan.textContent =
                parseInt(commentsCountSpan.textContent) - 1;

              if (parseInt(commentsCountSpan.textContent) === 0) {
                let noCommentsMessageContainer = document.getElementById(
                  "no-comments-message-container"
                );

                if (!noCommentsMessageContainer) {
                  noCommentsMessageContainer = document.createElement("div");
                  noCommentsMessageContainer.id =
                    "no-comments-message-container";
                  noCommentsMessageContainer.classList.add("col");
                  commentsList.prepend(noCommentsMessageContainer); 
                }

                let noCommentsMessageP =
                  noCommentsMessageContainer.querySelector(
                    "#no-comments-message"
                  );
                if (!noCommentsMessageP) {
                  noCommentsMessageP = document.createElement("p");
                  noCommentsMessageP.classList.add(
                    "text-muted",
                    "fst-italic",
                    "mb-4"
                  );
                  noCommentsMessageP.id = "no-comments-message";
                  noCommentsMessageP.textContent = "Ще немає коментарів.";
                  noCommentsMessageContainer.appendChild(noCommentsMessageP);
                }
              }
            }
          }
        } else {
          alert("Помилка при видаленні коментаря: " + result.message);
        }
      } catch (error) {
        console.error("Помилка при видаленні коментаря:", error);
        alert("Сталася помилка при видаленні коментаря. Спробуйте ще раз.");
      }
    }

    if (e.target.classList.contains("reply-comment-btn")) {
      const commentCard = e.target.closest(".col");
      const replyFormContainer = commentCard.querySelector(
        ".reply-comment-form-container"
      );
      const editButton = commentCard.querySelector(".edit-comment-btn");

      const isFormVisible = replyFormContainer.style.display === "block";
      replyFormContainer.style.display = isFormVisible ? "none" : "block";

      e.target.style.display = isFormVisible ? "inline-block" : "none";

      if (editButton) {
        editButton.style.display = isFormVisible ? "inline-block" : "none";
      }
    }

    if (e.target.classList.contains("cancel-reply-btn")) {
      const replyFormContainer = e.target.closest(
        ".reply-comment-form-container"
      );
      const commentCard = e.target.closest(".col");
      const replyButton = commentCard.querySelector(".reply-comment-btn");
      const editButton = commentCard.querySelector(".edit-comment-btn");

      replyFormContainer.style.display = "none";
      const textarea = replyFormContainer.querySelector(
        ".reply-comment-textarea"
      );
      textarea.value = "";
      const errorDiv = replyFormContainer.querySelector(
        ".reply-comment-error-message"
      );
      errorDiv.innerHTML = "";

      if (replyButton) {
        replyButton.style.display = "inline-block";
      }
      if (editButton) {
        editButton.style.display = "inline-block";
      }
    }

    if (e.target.classList.contains("submit-reply-btn")) {
      const replyFormContainer = e.target.closest(
        ".reply-comment-form-container"
      );
      const textarea = replyFormContainer.querySelector(
        ".reply-comment-textarea"
      );
      const errorDiv = replyFormContainer.querySelector(
        ".reply-comment-error-message"
      );
      const parentId = e.target.dataset.parentId;
      const content = textarea.value.trim();
      const postId = document.querySelector("input[name='post_id']").value;

      errorDiv.innerHTML = "";

      if (!content) {
        errorDiv.innerHTML = `<div class="alert alert-danger">Вміст відповіді не може бути порожнім.</div>`;
        return;
      }

      try {
        const response = await fetch("/comments/add", {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            post_id: postId,
            content: content,
            parent_id: parentId,
          }),
        });

        const result = await response.json();

        if (result.status === "success" && result.comment) {
          textarea.value = "";
          replyFormContainer.style.display = "none";

          const noCommentsMessageContainer = document.getElementById(
            "no-comments-message-container"
          );
          if (noCommentsMessageContainer) {
            noCommentsMessageContainer.remove();
          }

          const getCommentsResponse = await fetch("/comments/get", {
            method: "POST",
            headers: {
              "X-Requested-With": "XMLHttpRequest",
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              post_id: postId,
            }),
          });
          const commentsResult = await getCommentsResponse.json();

          if (commentsResult.status === "success" && commentsResult.comments) {
            commentsList.innerHTML = "";
            const renderedCommentsHtml = buildCommentsTreeHtml(
              commentsResult.comments,
              commentsResult.currentUser,
              null
            );
            commentsList.insertAdjacentHTML("beforeend", renderedCommentsHtml);

            const commentsCountSpan = document.getElementById("comments-count");
            if (commentsCountSpan) {
              commentsCountSpan.textContent = commentsResult.comments.length;
            }
          } else {
            errorDiv.innerHTML = `<div class="alert alert-danger">${
              commentsResult.message ||
              "Помилка при оновленні коментарів після відповіді."
            }</div>`;
          }
        } else {
          errorDiv.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
      } catch (error) {
        errorDiv.innerHTML = `<div class="alert alert-danger">Сталася помилка. Спробуйте ще раз.</div>`;
        console.error("Помилка при додаванні відповіді:", error);
      }
    }
  });
}
const addCommentForm = document.getElementById("add-comment-form");
if (addCommentForm) {
  addCommentForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const postId = formData.get("post_id");
    const commentContent = formData.get("content").trim();
    const errorMessageDiv = document.getElementById("add-comment-error");
    const commentsList = document.getElementById("comments-list");
    const commentsCountSpan = document.getElementById("comments-count");
    const noCommentsMessageContainer = document.getElementById(
      "no-comments-message-container"
    );

    errorMessageDiv.innerHTML = "";

    if (!commentContent) {
      showMessage(
        "Будь ласка, введіть текст коментаря.",
        "danger",
        "add-comment-error"
      );
      return;
    }

    try {
      const addResponse = await fetch("/comments/add", {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          post_id: postId,
          content: commentContent,
        }),
      });

      const addResult = await addResponse.json();
      if (addResult.status === "success" && addResult.comment) {
        this.reset();
        document.getElementById("commentContent").value = "";

        if (noCommentsMessageContainer) {
          noCommentsMessageContainer.remove();
        }

        const getCommentsResponse = await fetch("/comments/get", {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            post_id: postId,
          }),
        });

        const commentsResult = await getCommentsResponse.json();
        if (commentsResult.status === "success" && commentsResult.comments) {
          commentsList.innerHTML = "";
          const renderedCommentsHtml = buildCommentsTreeHtml(
            commentsResult.comments,
            commentsResult.currentUser,
            null
          );
          commentsList.insertAdjacentHTML("beforeend", renderedCommentsHtml);

          if (commentsCountSpan) {
            commentsCountSpan.textContent = commentsResult.comments.length;
          }
        } else {
          showMessage(
            commentsResult.message || "Помилка при отриманні коментарів.",
            "danger",
            "add-comment-error"
          );
        }
      } else {
        showMessage(
          addResult.message || "Помилка при додаванні коментаря.",
          "danger",
          "add-comment-error"
        );
      }
    } catch (error) {
      showMessage(
        "Сталася помилка. Спробуйте ще раз.",
        "danger",
        "add-comment-error"
      );
      console.error("Помилка при додаванні коментаря:", error);
    }
  });
}
