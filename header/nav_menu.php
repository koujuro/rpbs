<header>

    <nav id="navigation">
        <div id="accountName"></div>

        <div class="links">
            <?php
            if (SessionUtilities::getSession('UserType') === 'superAdmin')
                echo "<a href='super_admin_dashboard.php'>Podaci o firmama</a>
                      <a href='creating_company.php'>Kreiranje firme</a>
                      <a href='../log_out/log_out.php'>Log Out</a>";
            else
                echo "<a href='../dashboard/dashboard.php'>Home</a>
                    <a href='../notifications/notifications.php'>Planogram</a>
                    <a href='../map/map.php'>Mapa</a>
                    <div class='dropdown'>
                        <a href='../edit_devices/edit_devices.php'>Podaci o uredjajima</a>
                        <div class='dropdown-content'>
                            <a href='../edit_devices/edit_ppas.php'>Podaci o PP aparatima</a>
                            <a href='../edit_devices/edit_hydrants.php'>Podaci o Hidrantima</a>
                            <a href='../edit_devices/edit_upps.php'>Podaci o UPP</a>
                        </div>
                    </div>
        
                    <div class='dropdown'>
                        <a href='javascript:void(0)'>Podesavanja</a>
                        <div class='dropdown-content'>
                            <a href='../admin_panel/admin_panel.php'>Admin</a>
                            <a href='../users_data_edit/users_data_edit.php'>Operateri</a>
                            <a href='../client_object_edit/client_object_edit.php'>Korisnici</a>
                            <a href='../measuring_devices/measuring_devices.php'>Merni uredjaji</a>
                        </div>
                    </div>
                    <a href='../log_out/log_out.php'>Log Out</a>";
            ?>

        </div>

    </nav>

</header>
