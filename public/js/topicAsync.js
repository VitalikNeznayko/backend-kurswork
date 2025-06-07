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
const topicAdd = document.getElementById("topicAdd");
if (topicAdd) {
  topicAdd.addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const messageDiv = document.getElementById("addMessage");

    const categorySelect = document.getElementById("categorySelect");
    const category_id = categorySelect ? categorySelect.value : null;

    try {
      const response = await fetch("/topics/add", {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          title: formData.get("title"),
          content: formData.get("content"),
          category_id: category_id,
        }),
      });
      const result = await response.json();

      if (result.status === "success") {
        window.location.href = result.redirect;
      } else {
        showMessage(result.message, "danger", "addMessage");
      }
    } catch (error) {
      showMessage("Сталася помилка. Спробуйте ще раз.", "danger", "addMessage");
    }
  });
}

const editTopicBtn = document.getElementById("editTopicBtn");
if (editTopicBtn) {
  editTopicBtn.addEventListener("click", () => {
    const titleEl = document.getElementById("mainTitle");
    const contentEl = document.getElementById("mainContent");
    const categoryEl = document.querySelector(".badge.bg-info.text-dark");
    const backBtn = document.getElementById("backCategory");
    
    if (!titleEl || !contentEl || !categoryEl) return;

    const originalTitle = titleEl.innerText;
    const originalContent = contentEl.innerText;
    const originalCategoryName = categoryEl.innerText;

    const titleInput = document.createElement("input");
    titleInput.type = "text";
    titleInput.className = "form-control mb-3";
    titleInput.id = "editTitle";
    titleInput.value = originalTitle;

    editTopicBtn.style.display = "none";

    const contentTextarea = document.createElement("textarea");
    contentTextarea.className = "form-control mb-3";
    contentTextarea.id = "editContent";
    contentTextarea.rows = 8;
    contentTextarea.value = originalContent;

    const categorySelect = document.createElement("select");
    categorySelect.className = "form-select mb-3";
    categorySelect.id = "editCategory";
    categorySelect.name = "category_id";

    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = "Оберіть категорію";
    categorySelect.appendChild(defaultOption);

    const categoriesData = JSON.parse(editTopicBtn.dataset.categories || "[]");
    const currentCategoryName = categoryEl.innerText;
    const selected = categoriesData.find(
      (cat) => cat.name === currentCategoryName
    );
    const originalCategoryId = selected ? selected.id : "";

    if (categoriesData.length > 0) {
      categoriesData.forEach((category) => {
        const option = document.createElement("option");
        option.value = category.id;
        option.textContent = category.name || "Без назви";
        if (category.id == originalCategoryId) {
          option.selected = true;
        }
        categorySelect.appendChild(option);
      });
    }

    const saveBtn = document.createElement("button");
    saveBtn.className = "btn btn-success me-2";
    saveBtn.textContent = "Зберегти";

    const cancelBtn = document.createElement("button");
    cancelBtn.className = "btn btn-secondary";
    cancelBtn.textContent = "Скасувати";

    const messageDiv = document.createElement("div");
    messageDiv.className = "mt-2";
    messageDiv.id = "editMessage";

    titleEl.replaceWith(titleInput);
    contentEl.replaceWith(contentTextarea);
    categoryEl.replaceWith(categorySelect);

    contentTextarea.parentElement.appendChild(saveBtn);
    contentTextarea.parentElement.appendChild(cancelBtn);
    contentTextarea.parentElement.appendChild(messageDiv);

    cancelBtn.addEventListener("click", () => {
      titleInput.replaceWith(titleEl);
      contentTextarea.replaceWith(contentEl);
      categorySelect.replaceWith(categoryEl);
      editTopicBtn.style.display = "inline-block";
      saveBtn.remove();
      cancelBtn.remove();
      messageDiv.remove();
    });

    saveBtn.addEventListener("click", async () => {
      const newTitle = titleInput.value.trim();
      const newContent = contentTextarea.value.trim();
      const newCategoryId = categorySelect.value;

      try {
        const topicId = editTopicBtn.dataset.topicId;
        const response = await fetch(`/topics/edit/${topicId}`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            title: newTitle,
            content: newContent,
            category_id: newCategoryId,
          }),
        });
        const result = await response.json();
        if (result.status === "success") {
          showMessage("Успішно збережено", "success", "editMessage");
          titleEl.innerText = newTitle;
          contentEl.innerText = newContent;
          editTopicBtn.style.display = "inline-block";

          const updatedCategoryId = result.topic.category_id;

          const selectedCategory = categoriesData.find(
            (category) => category.id == updatedCategoryId
          );
          if (backBtn && selectedCategory) {
            backBtn.href = `/topics?category=${updatedCategoryId}`;
            backBtn.innerText = `Перейти до категорії "${selectedCategory.name}"`;
          }
          const updatedCategoryEl = document.createElement("span");
          updatedCategoryEl.className = "badge bg-info text-dark";
          updatedCategoryEl.innerText = selectedCategory
            ? selectedCategory.name
            : originalCategoryName;

          titleInput.replaceWith(titleEl);
          contentTextarea.replaceWith(contentEl);
          categorySelect.replaceWith(updatedCategoryEl);
          saveBtn.remove();
          cancelBtn.remove();
          messageDiv.remove();
        } else {
          showMessage(result.message, "danger", "editMessage");
        }
      } catch (err) {
        showMessage(
          "Сталася помилка при збереженні. Спробуйте ще раз.",
          "danger",
          "editMessage"
        );
      }
    });
  });
}
