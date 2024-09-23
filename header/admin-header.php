
<script src="/holidayhome/auth.js"></script>
<script src="/holidayhome/header/header.js"></script>

<header class="d-f-ai-c">
    <nav>
        <a href="/holidayhome/admin-view/gallery/gallery.php">My offerings</a>
        <a href="/holidayhome/admin-view/upload_house/upload_house.php">New offering</a>
        <a  onclick="showPopup()" class="account" ><img src="/holidayhome/img/user.png" alt=""></a>
    </nav>
</header>

<dialog id="account-popup" class="account-popup">
    <button onclick="logout()" class="btn-2">Logout</button>
    <button onclick="deleteAccount()" class="btn-2">Delete Account</button>
    <button onclick="closePopup()" class="btn-1">close</button>
</dialog>