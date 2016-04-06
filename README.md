# silty_ui
##Full-screen display for use on a Raspberrypi.
##With nginx installed allows for some childrens rewards system based on rules and rewards.

###Displays in full screen application:

Nginx status

Samba status

Mumble-server status

Deluge status

CPU usage

RAM usage

Uptime

Disk usage

Date and time

Section for notes

Section for "stars", reward system for children

#setup
```
sudo apt-get install git sqlite3 php5-sqlite python3-psutil python3-tk python3-pil.imaging
git clone http://github.com/formcore/silty_ui
./silty_ui/silty_ui.py
```

***
##PLANNED UPDATES:
 
* Set "stars" pane to show users individually, as opposed to totals shown currently.  


## Known bugs

* Uptime sometimes shows "garbage" numbers at the end of the string, this is something to do with the admittedly awful method of just cutting up a long string of time  
