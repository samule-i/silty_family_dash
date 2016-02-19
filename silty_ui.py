#!/usr/bin/env python3
import time
import datetime
from datetime import timedelta
import os
import psutil
import sqlite3
from tkinter import *
from PIL import ImageTk, Image
def current_dir():
    return os.path.dirname(os.path.realpath(sys.argv[0]))
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
def get_stars():
	database = '/mnt/share/public/html/lib/database.db'
	connect = sqlite3.connect(database)
	cursor = connect.cursor()
	cursor.execute('select count(*) from star_chart')
	total = cursor.fetchone()
	cursor.execute('select cost from rewards where not awarded_date = ""')
	rewards = cursor.fetchall()
	spent = sum(sum(tuple) for tuple in rewards)
	left = total[0] - spent
	if connect:
		connect.close()
	return str(left) +' / '+ str(total[0])

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
	
def update():
	global clock
	
	global cpu
	global ram
	global pi_uptime
	global share_used
	global share_left

	global nginx
	global samba
	global deluge
	global mumble
	
	disk_dir = '/mnt/share'
	
	currtime.config(text=time.strftime('%I:%M'))
	currdate.config(text=time.strftime('%d/%m/%Y'))
	cpu.config(text='cpu usage: '+str(cpu_usage())+'%')
	pi_uptime.config(text='uptime: '+uptime())
	vmem=psutil.virtual_memory()
	ram.config(text='ram usage: '+str(vmem.percent)+'%')
	share_usage=str(psutil.disk_usage(disk_dir).used>>30)[::1]+'gb'
	share_left=str(psutil.disk_usage(disk_dir).free>>30)[::1]+'gb'
	share_used.config(text='disk usage: '+share_usage)
	share_free.config(text='disk free: '+share_left)
	if status('nginx') == True:
		nginx.config(text='webserver', bg='#88A8A7', fg='#F4EDE3')
	else:
		nginx.config(text='webserver', bg='#e6005b')
	if status('samba') == True:
		samba.config(text='file share', bg='#88A8A7', fg='#F4EDE3')
	else:
		samba.config(text='file share', bg='#e6005b')
	if status('deluge') == True:
		deluge.config(text='torrent client', bg='#88A8A7', fg='#F4EDE3')
	else:
		deluge.config(text='torrent client', bg='#e6005b')
	if status('mumble') == True:
		mumble.config(text='voip', bg='#88A8A7', fg='#F4EDE3')
	else:
		mumble.config(text='voip', bg='#e6005b')
	
	#star_chart.config(text=get_stars())
	
	root.after(1000, update)

root = Tk()
root.title("silty_ui")
# make it cover the entire screen
w, h = root.winfo_screenwidth(), root.winfo_screenheight()
root.overrideredirect(1)
root.geometry("%dx%d+0+0" % (w, h))
root.focus_set() # <-- move focus to this widget
root.bind("<Escape>", lambda e: e.widget.quit())
root.config(cursor="none")

#create widgets
title = Label(root, text="silty", font=('FreeSans', 14), pady=5, bg='#BC3522', fg='#F4EDE3')
clock = Frame(root,pady=20,bg="#F5B43E")
currtime = Label(clock, font=('FreeSans', 40), bg="#F5B43E", fg='#F4EDE3')
currdate = Label(clock, font=('FreeSans', 14), bg="#F5B43E", fg='#F4EDE3')

left_panel = Frame(root, width=250)

left_panel_tabs = Frame(left_panel)
system_tab = Label(left_panel_tabs, text="system", font=('FreeSans', 14), anchor=W, activebackground="#88A8A7", activeforeground="#F4EDE3")
processes_tab = Label(left_panel_tabs, text="processes", font=('FreeSans', 14), anchor=W, activebackground="#88A8A7", activeforeground="#F4EDE3", state=ACTIVE)

processes = Frame(left_panel)
nginx = Label(processes, padx=12, font=('FreeSans', 18),anchor=W)
samba = Label(processes, padx=12, font=('FreeSans', 18),anchor=W)
mumble = Label(processes, padx=12, font=('FreeSans', 18),anchor=W)
deluge = Label(processes, padx=12, font=('FreeSans', 18),anchor=W)

system = Frame(left_panel)
cpu = Label(system, padx=12, font=('FreeSans', 14),anchor=W, bg='#88A8A7', fg="#F4EDE3")
pi_uptime = Label(system, padx=12, font=('FreeSans', 14),anchor=W, bg='#88A8A7', fg="#F4EDE3")
ram = Label(system, padx=12, font=('FreeSans', 14),anchor=W, bg='#88A8A7', fg="#F4EDE3")
share_used= Label(system, padx=12, font=('FreeSans', 14),anchor=W, bg='#88A8A7', fg="#F4EDE3")
share_free= Label(system, padx=12, font=('FreeSans', 14),anchor=W, bg='#88A8A7', fg="#F4EDE3")

stars = Frame(root, bg='#BC3522')

image_dir = current_dir()

curly_star=Image.open(image_dir+"curlystar.png")
curly_star=curly_star.resize((150,150), Image.ANTIALIAS)
silty_star_image = ImageTk.PhotoImage(curly_star)

silty_star = Label(stars, bg='#BC3522', fg='#F4EDE3', image = silty_star_image)
star_chart = Label(stars, font=('FreeSans', 22), bg='#BC3522', fg='#F4EDE3')

img = Image.open(image_dir+'img1.png')
img = img.resize((165,220), Image.ANTIALIAS)

pic = ImageTk.PhotoImage(img)
panel = Label(root, image = pic, bg='#88A8A7', highlightthickness=0, anchor=SE)

#binding
system_tab.bind("<Button-1>", left_panel_system)
processes_tab.bind("<Button-1>", left_panel_processes)

#Gridding
title.grid(row=0, column=0, columnspan=3, sticky=W+E)

left_panel.grid(row=1, column=0, rowspan=2, sticky=E+N+S+W)

left_panel_tabs.grid(row=0, column=0, sticky=W+E)
system_tab.grid(row=0, column=0, sticky=W+E)
processes_tab.grid(row=0, column=1, sticky=W+E)

processes.grid(row=1, column=0, sticky=W+E+N+S)
nginx.grid(sticky=E+W+N+S)
samba.grid(sticky=E+W+N+S)
mumble.grid(sticky=E+W+N+S)
deluge.grid(sticky=E+W+N+S)

clock.grid(row=1, column=1, sticky=E+W+N+S)
currtime.grid(sticky=W+E+S)
currdate.grid(sticky=W+E+N)

cpu.grid(sticky=E+W+N+S)
ram.grid(sticky=E+W+N+S)
pi_uptime.grid(sticky=E+W+N+S)
share_used.grid(sticky=E+W+N+S)
share_free.grid(sticky=E+W+N+S)

stars.grid(row=2, column=1, sticky=N+E+W+S, padx=0, pady=0, ipadx=0, ipady=0)
silty_star.grid(column=0, row=0, sticky=E+W+S, padx=0, pady=0, ipadx=0, ipady=0)
star_chart.grid(column=0, row=1, sticky=E+W+N, padx=0, pady=0, ipadx=0, ipady=0)

panel.grid(row=2, column=2, sticky=N+E+W+S, ipadx=0, ipady=0, padx=0, pady=0)

#Weighting
Grid.rowconfigure(root, 0, weight=0)
Grid.rowconfigure(root, 1, weight=1)
Grid.rowconfigure(root, 2, weight=1)

Grid.columnconfigure(root, 0, weight=1)
Grid.columnconfigure(root, 1, weight=2)
Grid.columnconfigure(root, 2, weight=1)

Grid.columnconfigure(left_panel, 0, weight=1)
Grid.rowconfigure(left_panel, 0, weight=0)
Grid.rowconfigure(left_panel, 1, weight=1)

Grid.columnconfigure(left_panel_tabs, 0, weight=1)
Grid.columnconfigure(left_panel_tabs, 1, weight=1)
Grid.rowconfigure(left_panel_tabs, 0, weight=0)

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
