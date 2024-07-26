document.getElementById('insurance-form').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const insuranceType = document.getElementById('insurance-type').value;
    const comments = document.getElementById('comments').value;

    const message = `
        <h2>Thank you for your submission!</h2>
        <p>Name: ${name}</p>
        <p>Email: ${email}</p>
        <p>Phone: ${phone}</p>
        <p>Insurance Type: ${insuranceType}</p>
        <p>Comments: ${comments}</p>
    `;

    document.getElementById('message').innerHTML = message;
    document.getElementById('insurance-form').reset();
});
