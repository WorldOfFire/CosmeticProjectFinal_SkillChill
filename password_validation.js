document.addEventListener("DOMContentLoaded", function () {
  const forms = document.querySelectorAll(".change-password-form");

  forms.forEach((form) => {
    const newPass = form.querySelector(".new-password");
    const repeatPass = form.querySelector(".new-repeat-password");
    const errorDiv = form.querySelector(".password-match-error");

    function validatePasswords() {
      if (newPass.value !== repeatPass.value) {
        errorDiv.style.display = "block";
        return false;
      } else {
        errorDiv.style.display = "none";
        return true;
      }
    }

    newPass.addEventListener("input", validatePasswords);
    repeatPass.addEventListener("input", validatePasswords);

    form.addEventListener("submit", function (e) {
      if (!validatePasswords()) {
        e.preventDefault();
      }
    });
  });
});
