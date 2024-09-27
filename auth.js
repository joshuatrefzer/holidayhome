
window.addEventListener('load', () => {
    skipAuth();
});


function skipAuth() {
    let currentUrl = window.location.href;
    if (user) {
        if (currentUrl.includes('login') || currentUrl.includes('sign-up')) {
            if (user.role === 'user') {
                window.location.href = '/holidayhome/user-view/user-view.php';
            }
            if (user.role === 'admin') {
                window.location.href = '/holidayhome/admin-view/gallery/gallery.php';
            }
        }
    }
}

function prepareSignUp(event, role) {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    const data = {
        username: username,
        password: password,
        role: role
    };

    signUp(data);
}


function signUp(data) {
    fetch('sign_up.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(responseData => {
            console.log(responseData);
            if (responseData.success) {
                setUser(responseData.user);
            } else {
                getFeedback('Error by creating user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}


function prepareLogin(event) {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    const data = {
        username: username,
        password: password,
    };

    login(data);
}


function login(data) {
    fetch('login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(responseData => {
            if (responseData.success) {
                setUser(responseData.user);
            } else {
                getFeedback('Please use valid data');
            }
        })
        .catch(error => {
            getFeedback('Something went wrong');
        });
}

function setUser(newUser) {
    user = newUser;
    localStorage.setItem('user', JSON.stringify(user));

    if (user.role === 'admin') {
        window.location.href = '/holidayhome/admin-view/gallery/gallery.php';
    } else if (user.role === 'user') {
        window.location.href = '/holidayhome/user-view/user-view.php';
    }
}

function logout() {
    localStorage.removeItem('user');
    window.location.href = '/holidayhome/index.html';
}


function deleteAccount() {
    const data = {
        id: user.id
    }
    deleteUser(data);
}

function deleteUser(data) {
    fetch('/holidayhome/delete_account.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(responseData => {
            if (responseData.success) {
                window.location.href = '/holidayhome/index.html';
            } else {
                getFeedback('Error by deleting user, you cannot delete the demo users!');
                console.log(responseData.message);

            }
        })
        .catch(error => {
            getFeedback('Error by deleting user');
        });
}