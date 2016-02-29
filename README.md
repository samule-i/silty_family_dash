# silty_ui
Full-screen display.

Display shows if the services Nginx, samba, mumble-server and deluged are running (if installed).

Requires sqlite3 to run.


It is possible that the database won't automatically add this user on older versions, if there's no silty user on the system, you could add in the user manually.

To use the some functions it may be required that you manually add a user to the database, use these commands to do so:

sqlite3 /path_to_program/main.db

INSERT INTO users username, password VALUES('silty', '$2y$10$wYhCB.JBxh62J9qcV4RAv.BMMdQsWcMLS5KK6R3MetKynDUdYxDUa');

This will set the database to include a user "silty" with the password "silty".

DON'T FORGET TO CHANGE THIS PASSWORD

You can change the password by logging in.
You can access the website by opening your browser and going to \\path_to_program\html.

Hopefully once you can log in, it shouldn't be too much of an issue to figure out the rest of it.

to change the image that shows in the lowest-right panel, replace the img1.png that's in the same directory as the script.

edit the variable disk_dir to set the location of the disk you want storage data on.

Shows uptime, cpu usage, ram usage and disk usage.
Shows service status for Nginx, Samba, Mumble-server and Deluge daemon.

Shows time and date.
