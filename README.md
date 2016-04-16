# silty_ui
##Full-screen display for use on a Raspberrypi.
##With nginx installed allows for some childrens rewards system based on rules and rewards.

###Displays in full screen application:

Status for various server services.

CPU / RAM usage

Uptime

Disk usage

Date and time

Section for notes

Section for "stars", reward system for children

Image gallery.

#setup
```
sudo apt-get install sqlite3 php5-sqlite python3-psutil python3-tk python3-pil python-imaging-tk
wget https://github.com/formcore/silty_family_dash/archive/master.zip
unzip master.zip
./silty_family_dash-master/silty_ui/silty_ui.py
```
***
##PLANNED UPDATES:
* Set "stars" pane to show users individually, as opposed to totals shown currently.  


## Known bugs
* Uptime sometimes shows "garbage" numbers at the end of the string, this is something to do with the admittedly awful method of just cutting up a long string of time  
