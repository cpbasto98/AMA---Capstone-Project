const scriptURL = "https://script.google.com/macros/s/AKfycbzLm5oc6CMHu2XeHVXY0XWVcWTlQj-EBzfAmoJFc5wpNz6M9yUvaPCKTW-_pWdA9lOL/exec";

// Disable past dates
const today = new Date().toISOString().split("T")[0];
document.querySelector('input[name="date"]').setAttribute("min", today);

document.getElementById("appointmentForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const submitMsg = document.getElementById("submitMessage");
  submitMsg.textContent = "Submitting...";
  submitMsg.classList.add("loading-dots");

  const formData = {
    name: this.name.value.trim(),
    contact: this.contact.value.trim(),
    service: this.service.value,
    date: this.date.value,
    time: this.time.value
  };

  // Validate empty fields (simple but effective)
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
    submitMsg.textContent = "Please fill in all required fields.";
    submitMsg.classList.remove("loading-dots");
    submitMsg.style.color = "red";
    return;
  }

  // Send to Google Script
  fetch(scriptURL, {
    method: "POST",
    body: JSON.stringify(formData)
  })
  .then(response => response.json())
  .then(data => {

    submitMsg.classList.remove("loading-dots");

    if (data.result === "success") {
      submitMsg.textContent = "Appointment submitted successfully!";
      submitMsg.style.color = "green";
      this.reset();
    } else {
      submitMsg.textContent = "Error submitting appointment.";
      submitMsg.style.color = "red";
    }
  })
  .catch(error => {
    submitMsg.textContent = "Network error. Please try again.";
    submitMsg.style.color = "red";
    submitMsg.classList.remove("loading-dots");
  });
});
