const scriptURL = "https://script.google.com/macros/s/AKfycbzLm5oc6CMHu2XeHVXY0XWVcWTlQj-EBzfAmoJFc5wpNz6M9yUvaPCKTW-_pWdA9lOL/exec";

const dateInput = document.querySelector('input[name="date"]');
const today = new Date().toISOString().split("T")[0];
dateInput.setAttribute("min", today);

// =============================
// 📞 CONTACT NUMBER FILTER + FORMAT + LIVE VALIDATION
// =============================
const contactInput = document.querySelector('input[name="contact"]');

// Format to: 0912-345-6789
function formatPhone(num) {
  num = num.replace(/[^0-9]/g, ""); // remove non-numbers

  if (num.length <= 4) {
    return num;
  } else if (num.length <= 7) {
    return num.slice(0, 4) + "-" + num.slice(4);
  } else {
    return num.slice(0, 4) + "-" + num.slice(4, 7) + "-" + num.slice(7, 11);
  }
}

contactInput.addEventListener("input", function () {
  const raw = this.value.replace(/[^0-9]/g, "");
  this.value = formatPhone(raw);

  // LIVE validation
  if (raw.length === 0) {
    this.classList.remove("valid-number", "invalid-number");
  } else if (raw.length === 11) {
    this.classList.add("valid-number");
    this.classList.remove("invalid-number");
  } else {
    this.classList.add("invalid-number");
    this.classList.remove("valid-number");
  }
});

// =============================
// FORM SUBMIT
// =============================
document.getElementById("appointmentForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const submitMsg = document.getElementById("submitMessage");
  submitMsg.textContent = "Submitting...";
  submitMsg.style.color = "#7a3e9d";

  const cleanContact = this.contact.value.replace(/[^0-9]/g, "");

  // Validate phone: Must be 11 digits
  if (cleanContact.length !== 11) {
    submitMsg.textContent = "Phone number must be 11 digits.";
    submitMsg.style.color = "red";
    contactInput.classList.add("invalid-number");
    return;
  }

  const formData = {
    name: this.name.value.trim(),
    contact: cleanContact,
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

    // Remove validation borders when form resets
    contactInput.classList.remove("valid-number", "invalid-number");

  })
  .catch(() => {
    submitMsg.textContent = "Network error. Please try again.";
    submitMsg.style.color = "red";
  });
});
