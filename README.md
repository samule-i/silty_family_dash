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

[Imgur](http://i.imgur.com/bJeqhTy.png)
[Imgur](http://i.imgur.com/pFhSWZd.png)
[Imgur](http://i.imgur.com/gei3jAf.png)
[Imgur](http://i.imgur.com/4MK5YX1.png)
[Imgur](http://i.imgur.com/zlsTaw0.png)

#setup
```
sudo apt-get install sqlite3 php5-sqlite python3-psutil python3-tk python3-pil python-imaging-tk
wget https://github.com/formcore/silty_family_dash/archive/master.zip
unzip master.zip
./silty_family_dash-master/silty_ui/silty_ui.py
```
