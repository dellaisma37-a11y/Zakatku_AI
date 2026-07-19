<header class="topbar">
    <form class="ai-search" id="aiSearchForm">
        <i class="ti ti-search"></i>
        <input type="text" name="pertanyaan" id="aiSearchInput" placeholder="Tanya AI tentang Zakat Maal..." autocomplete="off">
    </form>

    <div class="topbar-right">
        <div class="notif-wrap">
            <button class="icon-btn" id="btnNotif" aria-label="Notifikasi">
                <i class="ti ti-bell"></i>
                <span class="notif-dot"></span>
            </button>
            <div class="notif-dropdown" id="notifDropdown">
                <p class="notif-title">Notifikasi</p>
                <div class="notif-item">
                    <i class="ti ti-alert-circle"></i>
                    <div>
                        <p>Estimasi zakat jatuh tempo 12 hari lagi</p>
                        <span>2 jam yang lalu</span>
                    </div>
                </div>
                <div class="notif-item">
                    <i class="ti ti-sparkles"></i>
                    <div>
                        <p>AI menemukan insight baru untuk Anda</p>
                        <span>Kemarin</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="user-chip">
            <div class="avatar"><?= htmlspecialchars($user['avatar_initials']) ?></div>
            <div class="user-meta">
                <p class="user-name" id="headerUserName"><?= htmlspecialchars($user['full_name']) ?></p>
                <p class="user-tier"><?= htmlspecialchars($user['tier']) ?></p>
            </div>
        </div>
    </div>
</header>
