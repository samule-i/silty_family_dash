# silty_ui
Full-screen display for use on a Raspberrypi.
With nginx installed allows for some childrens rewards system based on rules and rewards.

Displays:

Nginx status

Samba status

Mumble-server status

Deluge status

CPU usage

RAM usage

Uptime

Disk usage (Defualted to /mnt/lib for my use case, may set it to default to root if there's no ext hdd setup)

Date and time

Section for notes (requires nginx)

Section for "stars", reward system for children (requires nginx)

Requires sqlite3 to run.
Requites nginx setup with the html folder set as the root to access the website functionality.
Runs on Python3

It is possible that the database won't automatically add this user on older versions, if there's no silty user on the system, you could add in the user manually.

To use the some functions it may be required that you manually add a user to the database, use these commands to do so:

sqlite3 /path_to_program/main.db

INSERT INTO users username, password VALUES('silty', '$2y$10$wYhCB.JBxh62J9qcV4RAv.BMMdQsWcMLS5KK6R3MetKynDUdYxDUa');

This will set the database to include a user "silty" with the password "silty".

DON'T FORGET TO CHANGE THIS PASSWORD

You can change the password by logging in.

Hopefully once you can log in, it shouldn't be too much of an issue to figure out the rest of it.

to change the image that shows in the lowest-right panel, replace the img1.png that's in the same directory as the script.

PLANNED UPDATES:

Find some use for the remaining panel, currently showing an image.

Set "stars" pane to show users individually, as opposed to totals shown currently

Update HTML section to allow:

adding new users

Changing usernames

Admin panel to see other users additions

Change "rewards" to use locally hosted images

update each section to only show non-admin users content relative to themselves

Add some functionality to add links to sidebar (currently only editable direct through Sqlite3)

Remove "icomoon" icons in favour of basic buttons

move "change password" to some sort of controls page

Move all "main" panels out of root and into a frame, allowing me to remove and replace with other "fullscreen" dialogues.

Figure out a way to set the "rules" to explain which users a particular rules applies to using a single column in the table, and only show rules that apply to the person viewing (unless admin)
