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
const loginForm = document.getElementById("loginForm");
if (loginForm) {
  loginForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const messageDiv = document.getElementById("loginMessage");

    try {
      const response = await fetch("/users/login", {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        body: formData,
      });
      const result = await response.json();

      if (result.status === "success") {
        window.location.href = result.redirect;
      } else {
        showMessage(result.message, "danger", "loginMessage");
      }
    } catch (error) {
      showMessage(
        "Сталася помилка. Спробуйте ще раз.",
        "danger",
        "loginMessage"
      );
    }
  });
}

const registerForm = document.getElementById("registerForm");
if (registerForm) {
  registerForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const messageDiv = document.getElementById("registerMessage");

    try {
      const response = await fetch("/users/register", {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        body: formData,
      });
      const result = await response.json();

      if (result.status === "success") {
        window.location.href = result.redirect;
      } else {
        showMessage(result.message, "danger", "registerMessage");
      }
    } catch (error) {
      showMessage(
        "Сталася помилка. Спробуйте ще раз.",
        "danger",
        "registerMessage"
      );
    }
  });
}

const avatarForm = document.getElementById("avatarForm");
if (avatarForm) {
  avatarForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const messageDiv = document.getElementById("avatarMessage");
    const avatarPreview = document.getElementById("avatarPreview");
    const avatarHeaderPreview = document.getElementById("avatarHeaderPreview");

    try {
      const response = await fetch("/users/profile", {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      });
      const result = await response.json();

      if (result.status === "success") {
        showMessage("Аватар успішно завантажено!", "success", "avatarMessage");
        if (avatarPreview) {
          avatarPreview.src = result.avatarPath + "?" + new Date().getTime();
          avatarHeaderPreview.src =
            result.avatarPath + "?" + new Date().getTime();
        }
      } else {
        showMessage(result.message, "danger", "avatarMessage");
      }
    } catch (error) {
      showMessage(
        "Сталася помилка. Спробуйте ще раз.",
        "danger",
        "avatarMessage"
      );
    }
  });
}

function toggleProfileEditMode(isEditing) {
  const container = document.getElementById("profileInfo");
  if (!container) {
    console.error("Елемент profileInfo не знайдено.");
    return;
  }

  const spans = container.querySelectorAll("span[data-field]");
  const passwordFields = document.querySelectorAll(".password-field");
  const editBtn = document.getElementById("editProfileBtn");
  const saveBtn = document.getElementById("saveProfileBtn");
  const cancelBtn = document.getElementById("cancelEditBtn");
  const currentActionBtn = editBtn || saveBtn;

  showMessage("", "", "editMessage");

  if (isEditing) {
    container.dataset.original = JSON.stringify(
      Array.from(spans).map((s) => ({
        field: s.dataset.field,
        value: s.textContent,
      }))
    );

    spans.forEach((span) => {
      const val = span.textContent === "—" ? "" : span.textContent;
      const input = document.createElement("input");
      input.type = span.dataset.field === "login" ? "email" : "text";
      input.name = span.dataset.field;
      input.value = val;
      input.classList.add("form-control", "mb-2");
      span.replaceWith(input);
    });
    passwordFields.forEach((field) => {
      field.style.display = "block";
      const input = field.querySelector("input");
      input.type = "password";
    });

    if (currentActionBtn) {
      currentActionBtn.textContent = "Зберегти";
      currentActionBtn.classList.remove("btn-outline-primary");
      currentActionBtn.classList.add("btn-primary");
      currentActionBtn.id = "saveProfileBtn";
      currentActionBtn.removeEventListener("click", () =>
        toggleProfileEditMode(true)
      );
      currentActionBtn.addEventListener("click", saveProfile);
    }

    if (!cancelBtn) {
      const newCancelBtn = document.createElement("button");
      newCancelBtn.textContent = "Скасувати";
      newCancelBtn.classList.add("btn", "btn-secondary", "ms-2");
      newCancelBtn.id = "cancelEditBtn";
      if (currentActionBtn) {
        currentActionBtn.after(newCancelBtn);
      }
      newCancelBtn.addEventListener("click", cancelEdit);
    }
  } else {
    const inputs = container.querySelectorAll("input[name]");
    inputs.forEach((input) => {
      const span = document.createElement("span");
      span.dataset.field = input.name;
      span.classList.add("text-muted");
      span.textContent = input.value || "—";
      input.replaceWith(span);
    });

    passwordFields.forEach((field) => {
      field.style.display = "none";
      const input = field.querySelector("input");
      if (input) {
        const span = document.createElement("span");
        span.dataset.field = input.name;
        span.classList.add("text-muted");
        span.textContent = "";
        input.replaceWith(span);
      }
    });

    if (currentActionBtn) {
      currentActionBtn.textContent = "Редагувати профіль";
      currentActionBtn.classList.remove("btn-primary");
      currentActionBtn.classList.add("btn-outline-primary");
      currentActionBtn.id = "editProfileBtn";

      currentActionBtn.removeEventListener("click", saveProfile);
      currentActionBtn.addEventListener("click", () =>
        toggleProfileEditMode(true)
      );
    }

    if (cancelBtn) {
      cancelBtn.removeEventListener("click", cancelEdit);
      cancelBtn.remove();
    }
  }
}

async function saveProfile(e) {
  e.preventDefault();

  const container = document.getElementById("profileInfo");
  if (!container) {
    console.error("Елемент profileInfo не знайдено для збереження.");
    showMessage(
      "Помилка: Не вдалося знайти контейнер профілю.",
      "danger",
      "editMessage"
    );
    return;
  }
  const inputs = container.querySelectorAll("input[name]");
  const data = {};
  inputs.forEach((input) => {
    data[input.name] = input.value.trim();
  });

  if (!data.login || !data.firstname || !data.lastname) {
    showMessage(
      "Будь ласка, заповніть усі обов'язкові поля",
      "danger",
      "editMessage"
    );
    return;
  }

  try {
    const response = await fetch("/users/edit", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify(data),
    });
    const result = await response.json();

    if (result.status === "success") {
      const updatedUser = result.user;
      const currentInputs = container.querySelectorAll("input[name]");

      currentInputs.forEach((input) => {
        const fieldName = input.name;
        const newSpan = document.createElement("span");
        newSpan.dataset.field = fieldName;
        newSpan.classList.add("text-muted");

        if (updatedUser[fieldName] !== undefined) {
          newSpan.textContent = updatedUser[fieldName];
        } else {
          newSpan.textContent = "—";
        }
        input.replaceWith(newSpan);
      });

      document.querySelectorAll(".password-field").forEach((field) => {
        field.style.display = "none";
      });

      toggleProfileEditMode(false);
      showMessage("Профіль успішно оновлено!", "success", "editMessage");
    } else {
      showMessage(result.message, "danger", "editMessage");
    }
  } catch (error) {
    console.error("Помилка при оновленні профілю:", error);
    showMessage(
      "Сталася помилка при оновленні профілю",
      "danger",
      "editMessage"
    );
  }
}

function cancelEdit() {
  toggleProfileEditMode(false);
}

const editProfileBtn = document.getElementById("editProfileBtn");
if (editProfileBtn) {
  editProfileBtn.addEventListener("click", () => toggleProfileEditMode(true));
}