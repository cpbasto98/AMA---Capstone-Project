const scriptURL = "https://script.google.com/macros/s/AKfycbzLm5oc6CMHu2XeHVXY0XWVcWTlQj-EBzfAmoJFc5wpNz6M9yUvaPCKTW-_pWdA9lOL/exec";

document.getElementById("appointmentForm").addEventListener("submit", function(e) {
  e.preventDefault();

  document.getElementById("submitMessage").textContent = "Submitting...";

  const formData = {
    name: this.name.value,
    contact: this.contact.value,
    service: this.service.value,
    date: this.date.value,
    time: this.time.value
  };

  fetch(scriptURL, {
    method: "POST",
    body: JSON.stringify(formData)
  })
  .then(response => {
    document.getElementById("submitMessage").textContent =
      "Appointment submitted successfully!";
    this.reset();
  })
  .catch(error => {
    document.getElementById("submitMessage").textContent =
      "Error submitting appointment. Please try again.";
  });
});
