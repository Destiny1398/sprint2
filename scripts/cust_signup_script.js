document.addEventListener('DOMContentLoaded', function () {
    var Form1 = document.getElementById("Form1");
    var Form2 = document.getElementById("Form2");
    var Form3 = document.getElementById("Form3");
    var Form4 = document.getElementById("Form4");

    var Next1 = document.getElementById("Next1");
    var Next2 = document.getElementById("Next2");
    var Next3 = document.getElementById("Next3");
    var Back1 = document.getElementById("Back1");
    var Back2 = document.getElementById("Back2");
    var Back3 = document.getElementById("Back3");
    var Complete = document.getElementById("Complete");
    var progress = document.getElementById("progress");

    var firstName = document.getElementById("firstName");
    var lastName = document.getElementById("lastName");
    var email = document.getElementById("email");
    var password = document.getElementById("password");
    var confirmPassword = document.getElementById("confirmPassword");

    var firstNameError = document.getElementById("firstNameError");
    var lastNameError = document.getElementById("lastNameError");
    var emailError = document.getElementById("emailError");
    var confirmPasswordError = document.getElementById("confirmPasswordError");

    var contactNo = document.getElementById("contactNo");
    var contactNoError = document.getElementById("contactNoError");

    var profileImage = document.getElementById("profileImage");

    var licenseNumber = document.getElementById("licenseNumber");
    var licenseNumberError = document.getElementById("licenseNumberError");
    var dob = document.getElementById("dob");
    var dobError = document.getElementById("dobError");

    var letter = document.getElementById("letter");
    var number = document.getElementById("number");
    var special = document.getElementById("special");

    var letterPattern = /[a-zA-Z]{4,}/;
    var numberPattern = /[0-9]/;
    var specialPattern = /[!@#$%^&*]/;

    function formatContactNo(value) {
        let cleaned = ('' + value).replace(/\D/g, '');
        let match = cleaned.match(/^(\d{0,3})(\d{0,3})(\d{0,4})$/);
        if (match) {
            let formatted = '(';
            if (match[1]) {
                formatted += match[1];
                if (match[1].length === 3) {
                    formatted += ') ';
                }
            }
            if (match[2]) {
                formatted += match[2];
                if (match[2].length === 3) {
                    formatted += ' - ';
                }
            }
            if (match[3]) {
                formatted += match[3];
            }
            return formatted;
        }
        return value;
    }

    function validateFirstName() {
        if (firstName.value === "") {
            firstNameError.textContent = "First Name is required";
            return false;
        } else if (!/^[a-zA-Z\s]+$/.test(firstName.value)) {
            firstNameError.textContent = "First Name should contain only letters and spaces";
            return false;
        } else {
            firstNameError.textContent = "";
            return true;
        }
    }

    function validateLastName() {
        if (lastName.value === "") {
            lastNameError.textContent = "Last Name is required";
            return false;
        } else if (!/^[a-zA-Z]+$/.test(lastName.value)) {
            lastNameError.textContent = "Last Name should contain only letters";
            return false;
        } else {
            lastNameError.textContent = "";
            return true;
        }
    }

    function validateEmail() {
        emailError.textContent = email.validationMessage;
        return email.checkValidity();
    }

    function validatePassword() {
        var valid = true;

        if (password.value === "") {
            confirmPasswordError.textContent = "Password is required";
            return false;
        }

        if (letterPattern.test(password.value)) {
            letter.classList.remove("invalid");
            letter.classList.add("valid");
            letter.querySelector(".indicator").textContent = "✔";
        } else {
            letter.classList.remove("valid");
            letter.classList.add("invalid");
            letter.querySelector(".indicator").textContent = "✗";
            valid = false;
        }

        if (numberPattern.test(password.value)) {
            number.classList.remove("invalid");
            number.classList.add("valid");
            number.querySelector(".indicator").textContent = "✔";
        } else {
            number.classList.remove("valid");
            number.classList.add("invalid");
            number.querySelector(".indicator").textContent = "✗";
            valid = false;
        }

        if (specialPattern.test(password.value)) {
            special.classList.remove("invalid");
            special.classList.add("valid");
            special.querySelector(".indicator").textContent = "✔";
        } else {
            special.classList.remove("valid");
            special.classList.add("invalid");
            special.querySelector(".indicator").textContent = "✗";
            valid = false;
        }

        return valid;
    }

    function validateConfirmPassword() {
        if (confirmPassword.value === "") {
            confirmPasswordError.textContent = "Confirm Password is required";
            return false;
        } else if (password.value !== confirmPassword.value) {
            confirmPasswordError.textContent = "Passwords do not match";
            confirmPasswordError.classList.remove("valid");
            return false;
        } else {
            confirmPasswordError.textContent = "Passwords match";
            confirmPasswordError.classList.add("valid");
            return true;
        }
    }

    function validateContactNo() {
        contactNo.value = formatContactNo(contactNo.value);
        let cleaned = contactNo.value.replace(/\D/g, '');
        if (cleaned.length !== 10) {
            contactNoError.textContent = "Contact No should be 10 digits long";
            return false;
        } else {
            contactNoError.textContent = "";
            return true;
        }
    }
    

    function validateLicenseNumber() {
        if (licenseNumber.value === "") {
            licenseNumberError.textContent = "Driver's License Number is required";
            return false;
        } else {
            licenseNumberError.textContent = "";
            return true;
        }
    }

    function validateDOB() {
        if (dob.value === "") {
            dobError.textContent = "Date of Birth is required";
            return false;
        } else {
            dobError.textContent = "";
            return true;
        }
    }

    var today = new Date();
    var maxDate = today.toISOString().split("T")[0];
    dob.setAttribute("max", maxDate);

    password.onkeyup = function () {
        validatePassword();
        validateConfirmPassword();
    };

    confirmPassword.onkeyup = validateConfirmPassword;

    contactNo.onkeyup = validateContactNo;

    Next1.onclick = function() {
        var valid = validateFirstName() && validateLastName() && validateEmail() && validatePassword() && validateConfirmPassword();
        if (valid) {
            Form1.style.left = "-100%";
            Form2.style.left = "10%";
            progress.style.width = "50%";
        }
    }

    Back1.onclick = function() {
        Form1.style.left = "10%";
        Form2.style.left = "100%";
        progress.style.width = "25%";
    }

    Next2.onclick = function() {
        if (validateContactNo()) {
            Form2.style.left = "-100%";
            Form3.style.left = "10%";
            progress.style.width = "75%";
        }
    }

    Back2.onclick = function() {
        Form2.style.left = "10%";
        Form3.style.left = "100%";
        progress.style.width = "50%";
    }

    Next3.onclick = function() {
        if (validateLicenseNumber() && validateDOB()) {
            document.getElementById('firstNameField').value = firstName.value;
            document.getElementById('lastNameField').value = lastName.value;
            document.getElementById('emailField').value = email.value;
            document.getElementById('passwordField').value = password.value;
            document.getElementById('contactNoField').value = contactNo.value;

            if (profileImage.files.length > 0) {
                document.getElementById('profileImagePathField').value = profileImage.files[0].name;
            } else {
                document.getElementById('profileImagePathField').value = "default-profile.png";
            }

            // Disable the Next3 button to prevent multiple submissions
            Next3.disabled = true;

            // Submit the form via AJAX
            var formData = new FormData(Form3);

            fetch('../customer/cust_signup_submit_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Fetch and update Form 4 with the verification details
                fetchVerificationDetails();

                // Move to Form4
                Form3.style.left = "-100%";
                Form4.style.left = "10%";
                progress.style.width = "100%";

                // Re-enable the Next3 button after the process is complete
                Next3.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                // Re-enable the Next3 button if there's an error
                Next3.disabled = false;
            });
        }
    }

    Back3.onclick = function() {
        Form3.style.left = "10%";
        Form4.style.left = "100%";
        progress.style.width = "75%";
    }

    document.getElementById("togglePassword").addEventListener("click", function () {
        var type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    document.getElementById("toggleConfirmPassword").addEventListener("click", function () {
        var type = confirmPassword.getAttribute("type") === "password" ? "text" : "password";
        confirmPassword.setAttribute("type", type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    Form3.addEventListener("submit", function(event) {
        if (!validateLicenseNumber() || !validateDOB()) {
            event.preventDefault();
        } else {
            document.getElementById('firstNameField').value = firstName.value;
            document.getElementById('lastNameField').value = lastName.value;
            document.getElementById('emailField').value = email.value;
            document.getElementById('passwordField').value = password.value;
            document.getElementById('contactNoField').value = contactNo.value;
            if (profileImage.files.length > 0) {
                document.getElementById('profileImagePathField').value = profileImage.files[0].name;
            } else {
                document.getElementById('profileImagePathField').value = "default-profile.png";
            }
        }
    });

    function fetchVerificationDetails() {
        fetch('../customer/fetch_verification_details.php')
            .then(response => response.json())
            .then(details => {
                var verificationDetails = document.getElementById("verificationDetails");
                if (details && Object.keys(details).length > 0) {
                    verificationDetails.innerHTML = `
                        <p><strong>Valid From:</strong> ${details.valid_from}</p>
                        <p><strong>Valid To:</strong> ${details.valid_to}</p>
                        <p><strong>Relative Name:</strong> ${details.relative_name}</p>
                        <p><strong>State:</strong> ${details.state}</p>
                        <div class="result-group">
                            <label>COV Details:</label>
                            <ul class="cov-details">
                                ${details.cov_details.map(cov => `<li>COV: ${cov.cov}, Issue Date: ${cov.issue_date}</li>`).join('')}
                            </ul>
                        </div>
                    `;
                } else {
                    verificationDetails.innerHTML = "<p>No verification details available.</p>";
                }
            })
            .catch(error => console.error('Error fetching verification details:', error));
    }

    // Fetch verification details when Form 4 is displayed
    if (window.location.hash === '#Form4') {
        fetchVerificationDetails();
    }

    Complete.onclick = function() {
        window.location.href = '../customer/customer_homepage.php';
    };
});
