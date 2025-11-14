const scriptURL = "https://script.google.com/macros/s/AKfycbzLm5oc6CMHu2XeHVXY0XWVcWTlQj-EBzfAmoJFc5wpNz6M9yUvaPCKTW-_pWdA9lOL/exec";

const dateInput = document.querySelector('input[name="date"]');
const today = new Date().toISOString().split("T")[0];
dateInput.setAttribute("min", today);

document.getElementById("appointmentForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const submitMsg = document.getElementById("submitMessage");
  submitMsg.textContent = "Submitting...";
  submitMsg.style.color = "#7a3e9d";

  const formData = {
    name: this.name.value.trim(),
    contact: this.contact.value.trim(),
    service: this.service.value,
    date: this.date.value,
    time: this.time.value
  };

  // Basic validation
  let isValid = true;
  document.querySelectorAll("#appointmentForm input, #appointmentForm select").forEach(field => {
    if (!field.value) {
      field.classList.add("input-error");
      isValid = false;
    } else {
      field.classList.remove("input-error");
    }
  });

  if (!isValid) {
    submitMsg.textContent = "Please fill in all fields.";
    submitMsg.style.color = "red";
    return;
  }

  // Send data to Google Script (NO CORS ERROR)
  fetch(scriptURL, {
    method: "POST",
    mode: "no-cors",
    body: JSON.stringify(formData)
  })
  .then(() => {
    submitMsg.textContent = "Appointment submitted successfully!";
    submitMsg.style.color = "green";
    this.reset();
  })
  .catch(() => {
    submitMsg.textContent = "Network error. Please try again.";
    submitMsg.style.color = "red";
  });
});
