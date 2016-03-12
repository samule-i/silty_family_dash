#!/usr/bin/env python3
import time
import datetime
from datetime import timedelta
import os
import psutil
import sqlite3
from tkinter import *
from PIL import ImageTk, Image

def current_path():
    return os.path.dirname(os.path.realpath(sys.argv[0]))

def initialise_db():
	db_path = current_path()+'/main.db'
	connect = sqlite3.connect(db_path)
	cursor = connect.cursor()
	sql_script = open(current_path()+'/schema.sql', 'r')
	cursor.executescript(sql_script.read())

def close_window(event):
	global root
	root.destroy()

# toggle between system / processes window
def left_panel_system(event):
	global system
	global processes
	system_tab.config(state=ACTIVE)
	processes_tab.config(state=NORMAL)
	processes.grid_forget()
	system.grid(row=1, sticky=E+W+N+S)

def left_panel_processes(event):
	global system
	global processes
	processes_tab.config(state=ACTIVE)
	system_tab.config(state=NORMAL)
	system.grid_forget()
	processes.grid(row=1, sticky=E+W+N+S)

#get data for updates
def get_stars():
	db_path = current_path()+'/main.db'
	connect = sqlite3.connect(db_path)
	cursor = connect.cursor()
	try:
	    cursor.execute('select count(*) from stars')
	except OperationalError:
		time.sleep(4000)
		pass
	total = cursor.fetchone()
	try:
		cursor.execute('select cost from rewards where not award_date = ""')
	except OperationalError:
		time.sleep(4000)
		pass
	rewards = cursor.fetchall()
	spent = sum(sum(tuple) for tuple in rewards)
	left = total[0] - spent
	if connect:
		connect.close()
	return str(left) +' / '+ str(total[0])

def get_notes():
	db_path = current_path()+'/main.db'
	connect = sqlite3.connect(db_path)
	cursor = connect.cursor()
	cursor.execute('select username, title from notes order by id desc limit 10')
	results = cursor.fetchall()
	if connect:
		connect.close()
	return results

def status(program):
	if program == 'nginx':
		path = '/var/run/nginx.pid'
	if program == 'samba':
		path = '/var/run/samba/smbd.pid'
	if program == 'mumble':
		path = '/var/run/mumble-server/mumble-server.pid'
	if program == 'deluge':
		path='/home/pi/.config/deluge/deluged.pid'
	if os.path.isfile(path):
		return True
	else:
		return False
def uptime():
	with open('/proc/uptime', 'r') as f:
		uptime_seconds = float(f.readline().split()[0])
		uptime_string = str(timedelta(seconds = uptime_seconds))[:-10]
	return uptime_string

def cpu_usage():
    return psutil.cpu_percent()

#colour refs
orange = "#F7A975"
lilac = "#AAA3E0"
green = "#80DB77"
slate = "#16132B"
forest = "#0A1909"


# update sections with current data.
def clock_update():
    global clock
    currtime.config(text=time.strftime('%I:%M'))
    currdate.config(text=time.strftime('%d/%m/%Y'))

def processes_update():
	global nginx
	global samba
	global deluge
	global mumble
	if status('nginx') == True:
		nginx.config(text='webserver', bg=orange, fg=slate)
	else:
		nginx.config(text='webserver', bg='#e6005b')
	if status('samba') == True:
		samba.config(text='file share', bg=orange, fg=slate)
	else:
		samba.config(text='file share', bg='#e6005b')
	if status('deluge') == True:
		deluge.config(text='torrent client', bg=orange, fg=slate)
	else:
		deluge.config(text='torrent client', bg='#e6005b')
	if status('mumble') == True:
		mumble.config(text='voip', bg=orange, fg=slate)
	else:
		mumble.config(text='voip', bg='#e6005b')

def system_update():
    global cpu
    global ram
    global pi_uptime
    global share_used
    global share_left

    if os.path.isdir("/mnt/share"):
        share_usage=str(psutil.disk_usage('/mnt/share/').used>>30)[::1]+'gb'
        share_left=str(psutil.disk_usage('/mnt/share/').free>>30)[::1]+'gb'
    else:
        share_usage=str(psutil.disk_usage('/').used>>30)[::1]+'gb'
        share_left=str(psutil.disk_usage('/').free>>30)[::1]+'gb'

    cpu.config(text='cpu usage: '+str(cpu_usage())+'%')
    pi_uptime.config(text='uptime: '+uptime())
    vmem=psutil.virtual_memory()
    ram.config(text='ram usage: '+str(vmem.percent)+'%')
    #make an if loop using isdir function (os.path.isdir($path))

    share_used.config(text='disk usage: '+share_usage)
    share_free.config(text='disk free: '+share_left)

def stars_update():
    star_chart.config(text=get_stars())

def note_update():
    global note1
    global note2
    global note3
    global note4
    global note5
    global note6
    global note7
    global note8
    global note9
    global note10
    results = get_notes()
    i = 0
    for child in note.winfo_children():
        try:
            child.config(text=results[i][0] +': '+ results[i][1][:20:])
        except IndexError:
            pass
        i+=1

def update():
    clock_update()
    processes_update()
    system_update()
    stars_update()
    note_update()
    root.after(2000, update)

#test database existence, if not create it..
if not os.path.isfile(current_path()+'/main.db'):
    print("no db")
    initialise_db()

root = Tk()
root.title("silty_ui")
# make it cover the entire screen
w, h = root.winfo_screenwidth(), root.winfo_screenheight()
root.overrideredirect(1)
root.geometry("%dx%d+0+0" % (w, h))
root.focus_set() # <-- move focus to this widget
root.bind("<Escape>", lambda e: e.widget.quit())
root.config(cursor="none")

#create widgets w/ various styling attributes
title = Label(root,
    text="♥ silty ♥",
    font=('FreeSans', 14),
    bg=orange,
    fg=slate)
exit = Label(root, text="quit",
    font=('FreeSans', 14),
    bg=orange,
    fg=slate)

front = Frame(root)
clock = Frame(front, pady=20, bg=lilac)
currtime = Label(clock, font=('FreeSans', 40), bg=lilac, fg=slate)
currdate = Label(clock, font=('FreeSans', 14), bg=lilac, fg=slate)

left_panel = Frame(front, width=250)

left_panel_tabs = Frame(left_panel)
system_tab = Label(left_panel_tabs,
    text="system",
    font=('FreeSans', 14),
    anchor=W,
    activebackground=orange,
    activeforeground=slate,
    state=ACTIVE)

processes_tab = Label(left_panel_tabs,
    text="processes",
    font=('FreeSans', 14),
    anchor=W,
    activebackground=orange,
    activeforeground=slate)

processes = Frame(left_panel)
system = Frame(left_panel)
note = Frame(front, bg=green)
stars = Frame(front, bg=green)

nginx = Label(processes)
samba = Label(processes)
mumble = Label(processes)
deluge = Label(processes)

cpu = Label(system)
pi_uptime = Label(system)
ram = Label(system)
share_used= Label(system)
share_free= Label(system)

image_dir = current_path()+'/img'
curly_star=Image.open(image_dir+"/curlystar.png")
curly_star=curly_star.resize((150,150), Image.ANTIALIAS)
silty_star_image = ImageTk.PhotoImage(curly_star)

silty_star = Label(stars, bg=green, fg=forest, image = silty_star_image)
star_chart = Label(stars, font=('FreeSans', 22), bg=green, fg=forest)

note1 = Label(note)
note2 = Label(note)
note3 = Label(note)
note4 = Label(note)
note5 = Label(note)
note6 = Label(note)
note7 = Label(note)
note8 = Label(note)
note9 = Label(note)
note10 = Label(note)

for child in system.winfo_children():
    child.config(padx=12, font=('FreeSans', 14),
    anchor=W, bg=orange, fg=slate)
for child in processes.winfo_children():
    child.config(padx=12, font=('FreeSans', 18), anchor=W)
for child in note.winfo_children():
    child.config(anchor=W, font=('FreeSans', 12), bg=green, fg=forest)


img = Image.open(image_dir+"/img.png")
img = img.resize((220,220), Image.ANTIALIAS)
pic = ImageTk.PhotoImage(img)

panel = Label(front, image = pic, bg=orange, anchor=SE)

#binding
system_tab.bind("<Button-1>", left_panel_system)
processes_tab.bind("<Button-1>", left_panel_processes)
exit.bind("<Button-1>", close_window)

#Gridding
title.grid(row=0, column=0, sticky=W+E)
exit.grid(row=0, column=1, sticky=W+E)
front.grid(columnspan=2, sticky=N+E+W+S)
left_panel.grid(row=1, column=0, rowspan=2, sticky=E+N+S+W)

left_panel_tabs.grid(row=0, column=0, sticky=W+E)
system_tab.grid(row=0, column=0, sticky=W+E)
processes_tab.grid(row=0, column=1, sticky=W+E)

system.grid(row=1, column=0, sticky=W+E+N+S)
for child in processes.winfo_children():
    child.grid(sticky=N+E+W+S)

clock.grid(row=1, column=1, sticky=E+W+N+S)
currtime.grid(sticky=W+E+S)
currdate.grid(sticky=W+E+N)

for child in system.winfo_children():
    child.grid(sticky=N+E+W+S)

stars.grid(row=2, column=1, sticky=N+E+W+S)
silty_star.grid(sticky=E+W+S)
star_chart.grid(sticky=E+W+N)

note.grid(column=2, row=1, sticky=N+E+W+S)
for child in note.winfo_children():
    child.grid(sticky=N+E+W+S)

panel.grid(row=2, column=2, sticky=N+E+W+S)

#Weighting


Grid.rowconfigure(root, 0, weight=0)
Grid.rowconfigure(root, 1, weight=1)

Grid.columnconfigure(root, 0, weight=3)
Grid.columnconfigure(root, 1, weight=2)

Grid.columnconfigure(front, 0, weight=1)
Grid.columnconfigure(front, 1, weight=1)
Grid.columnconfigure(front, 2, weight=1)
Grid.rowconfigure(front, 0, weight=1)
Grid.rowconfigure(front, 1, weight=1)

Grid.columnconfigure(left_panel, 0, weight=1)
Grid.rowconfigure(left_panel, 1, weight=1)

Grid.columnconfigure(left_panel_tabs, 0, weight=1)
Grid.columnconfigure(left_panel_tabs, 1, weight=1)

Grid.columnconfigure(processes, 0, weight=1)
Grid.rowconfigure(processes, 0, weight=1)
Grid.rowconfigure(processes, 1, weight=1)
Grid.rowconfigure(processes, 2, weight=1)
Grid.rowconfigure(processes, 3, weight=1)

Grid.columnconfigure(system, 0, weight=2)
Grid.rowconfigure(system, 0, weight=1)
Grid.rowconfigure(system, 1, weight=1)
Grid.rowconfigure(system, 2, weight=1)
Grid.rowconfigure(system, 3, weight=1)
Grid.rowconfigure(system, 4, weight=1)

Grid.columnconfigure(stars, 0, weight=1)
Grid.rowconfigure(stars, 0, weight=1)
Grid.rowconfigure(stars, 1, weight=1)

Grid.columnconfigure(clock, 0, weight=3)
Grid.rowconfigure(clock, 0, weight=1)
Grid.rowconfigure(clock, 1, weight=1)

#finish
root.after(10, update)
root.mainloop()



#testing testin gone two thrww. This keyboard feels like shit man,  how do you cope????????? My left testicke us vufgger than my right one which is pretty stranghe cibsudering I'm a gotl
