#!/usr/bin/env python3
import time
import datetime
import random
from datetime import timedelta
from time import strftime
import os
import psutil
import sqlite3
from tkinter import *
from PIL import ImageTk, Image



#theming
theme = 'celty'

if theme == 'celty':
    colour_1 = '#333233'
    colour_2 = '#202021'
    colour_3 = '#424042'
    colour_4 = '#BFFF65'
    colour_5 = '#71FFFD'

if theme == 'asuka':
    colour_1 = '#711B25'
    colour_2 = '#220E1C'
    colour_3 = '#A72532'
    colour_4 = '#FF8A8F'
    colour_5 = '#FFCB95'

if theme == 'senketsu':
    colour_1 = '#240706'
    colour_2 = '#141408'
    colour_3 = '#011017'
    colour_4 = '#FF4F54'
    colour_5 = '#0897FF'

if theme == 'super hacker daru':
    colour_1 = '#00131C'
    colour_2 = '#031B33'
    colour_3 = '#032C33'
    colour_4 = '#86A3E3'
    colour_5 = '#0BDBD1'

if theme == 'kyubey':
    colour_1 = '#DEBDED'
    colour_2 = '#BA8BB2'
    colour_3 = '#A798CC'
    colour_4 = '#120D0E'
    colour_5 = '#131419'

if theme == 'starburst':
    colour_1 = '#F7A975'
    colour_2 = '#AAA3E0'
    colour_3 = '#80DB77'
    colour_4 = '#16132B'
    colour_5 = '#0A1909'


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
def list_users():
    userlist = []
    db_path = current_path()+'/main.db'
    connect = sqlite3.connect(db_path)
    cursor = connect.cursor()
    try:
        cursor.execute('SELECT username FROM users WHERE NOT id="1"')
    except sqlite3.OperationalError:
        pass
    for (x,) in cursor.fetchall():
        userlist.append(x)
    return userlist


def user_stars():
    total_stars = {}
    db_path = current_path()+'/main.db'
    connect = sqlite3.connect(db_path)
    cursor = connect.cursor()
    for user in list_users():
        try:
            cursor.execute('SELECT count(*) FROM stars WHERE owner = ?', [user])
            (r,) = cursor.fetchone()
            total_stars.update({user:r})
        except sqlite3.OperationalError:
            pass
    return total_stars


def user_spent():
    total_spend = {}
    db_path = current_path()+'/main.db'
    connect = sqlite3.connect(db_path)
    cursor = connect.cursor()
    for user in list_users():
        try:
            cursor.execute('SELECT cost FROM rewards WHERE owner = ? AND NOT award_date=""', [user])
            t=0
            for (x,) in cursor.fetchall():
                try:
                    t += x
                except TypeError:
                    pass
            cursor.execute('SELECT cost FROM rewards_archive WHERE owner = ? AND NOT award_date = ""', [user])
            for (x,) in cursor.fetchall():
                try:
                    t += x
                except TypeError:
                    pass
        except sqlite3.OperationalError:
            pass
        total_spend.update({user:t})
    return total_spend


def get_stars():
	db_path = current_path()+'/main.db'
	connect = sqlite3.connect(db_path)
	cursor = connect.cursor()
	try:
	    cursor.execute('select count(*) from stars')
	except sqlite3.OperationalError:
		pass
	total = cursor.fetchone()
	try:
		cursor.execute('select cost from rewards where not award_date = ""')
	except sqlite3.OperationalError:
		pass
	rewards = cursor.fetchall()
	spent = sum(sum(tuple) for tuple in rewards)
	left = total[0] - spent
	if connect:
		connect.close()
	return str(left) + ' / ' + str(total[0])


def get_notes():
    db_path = current_path()+'/main.db'
    connect = sqlite3.connect(db_path)
    cursor = connect.cursor()
    t=datetime.date(int(time.strftime("%Y")), int(strftime("%m")), int(time.strftime("%d")))
    today=str(time.mktime(t.timetuple()))[:-2:]
    try:
        cursor.execute('select id from timetable where date = ?', (str(today),))
    except sqlite3.OperationalError:
        pass
    req_id = cursor.fetchall()
    try:
        #cursor.execute('select date, time from timetable order by id desc limit 7 offset = ?', req_id[0][0])
        cursor.execute('select date, time from timetable where id >= ? limit 7', (str(req_id[0][0]),))
    except sqlite3.OperationalError:
        pass
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
		path = '/home/pi/.config/deluge/deluged.pid'
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


# update sections with current data.
def clock_update():
    global clock
    currtime.config(text=time.strftime('%I:%M'))
    currdate.config(text=time.strftime('%d/%m/%Y'))
    root.after(5000, clock_update)


def processes_update():
    '''
    access pre-determined PID files and update processes frame if they existence
    '''
    global nginx
    global samba
    global deluge
    global mumble
    if status('nginx') == True:
        nginx.config(text='webserver', bg=colour_1, fg=colour_4)
    else:
        nginx.config(text='webserver', bg='#e6005b')
    if status('samba') == True:
        samba.config(text='file share', bg=colour_1, fg=colour_4)
    else:
        samba.config(text='file share', bg='#e6005b')
    if status('deluge') == True:
        deluge.config(text='torrent client', bg=colour_1, fg=colour_4)
    else:
        deluge.config(text='torrent client', bg='#e6005b')
    if status('mumble') == True:
        mumble.config(text='voip', bg=colour_1, fg=colour_4)
    else:
        mumble.config(text='voip', bg='#e6005b')
    root.after(10000, processes_update)


def system_update():
    '''
    Update system frame with various stats from psutil.
    '''
    global cpu
    global ram
    global pi_uptime
    global share_used
    global share_left
    if os.path.isdir('/mnt/share'):
        share_usage = str(psutil.disk_usage('/mnt/share/').used>>30)[::1] + 'gb'
        share_left = str(psutil.disk_usage('/mnt/share/').free>>30)[::1] + 'gb'
    else:
        share_usage = str(psutil.disk_usage('/').used>>30)[::1] + 'gb'
        share_left = str(psutil.disk_usage('/').free>>30)[::1] + 'gb'
    cpu.config(text='cpu usage: ' + str(cpu_usage()) + '%')
    pi_uptime.config(text='uptime: ' + uptime())
    vmem=psutil.virtual_memory()
    ram.config(text='ram usage: ' + str(vmem.percent) + '%')
    share_used.config(text='disk usage: ' + share_usage)
    share_free.config(text='disk free: ' + share_left)
    #update system window every second
    root.after(1000, system_update)


def stars_update():
    '''
    Access the database to pull the stars and spent for each user.
    Update the stars frame with updated information.
    '''
    i = 0
    userlist = list_users()
    for child in user_list_frame.winfo_children():
        #get current users star total
        c_star_total = str(user_stars()[userlist[i]])
        #get current users total spend
        c_star_spend = str(user_stars()[userlist[i]] - user_spent()[userlist[i]])
        #create string for current user to each label, sadly these labels are created when the program is run and doesn't update for more users.
        child.config(text=userlist[i] + ':' + c_star_spend + '/' + c_star_total)
        i += 1
    #update stars every 30 seconds
    root.after(30000, stars_update)


def note_update():
    '''
    Acess the database and get a set number of rows, update the notes window
    with the rows from the database.
    '''
    global note1
    global note2
    global note3
    global note4
    global note5
    global note6
    global note7
    results = get_notes()
    i = 0
    for child in note.winfo_children():
        try:
            #run through each label and write the next note into it.
            child.config(text=datetime.datetime.fromtimestamp(int(results[i][0])).strftime("%a %d") + ': ' + results[i][1])
        except IndexError:
            pass
        i += 1
        #update every five seconds
    root.after(5000, note_update)


def gallery_widget():
    '''
    Access a database and count the number of rows in the table
    Generate a random number within the amount of rows and access that row.
    Access the database and get the image location.
    Prepare the image and update the gallery widget with the new image.
    '''
    global gallery
    global panel
    db_path = current_path() + '/main.db'
    connect = sqlite3.connect(db_path)
    cursor = connect.cursor()
    try:
        cursor.execute('SELECT count(*) FROM gallery')
    except sqlite3.OperationalError:
        pass
    amount = sum(cursor.fetchone())
    #create a random number between one and the amount of images in the gallery table
    rand_row=random.randint(1, amount)
    try:
        cursor.execute('SELECT image FROM gallery WHERE id = ?', [str(rand_row)])
        result = cursor.fetchone()
        image_path = result[0]
        img = Image.open(current_path() + '/html' + image_path)
    except sqlite3.OperationalError:
        pass
    if 'img' not in locals():
        img = Image.open(current_path() + '/img/img.png')
    #Get the highest of width or height and make that the size of the frame
    f_height, f_width=gallery.winfo_height(), gallery.winfo_width()
    if img.size[0] > img.size[1]:
        wpercent = (f_width / float(img.size[0]))
        g_height=int(float(img.size[1]) * float(wpercent))
        g_width=f_width
    else:
        hpercent = (f_height / float(img.size[1]))
        g_width=int(float(img.size[0]) * float(hpercent))
        g_height=f_height
    img = img.resize((g_width, g_height), Image.ANTIALIAS)
    gallery_image = ImageTk.PhotoImage(img)
    panel.config(image=gallery_image)
    panel.image = gallery_image
    #update every ten seconds
    root.after(10000, gallery_widget)


#test database existence, if not create it..
if not os.path.isfile(current_path() + '/main.db'):
    print('no db')
    initialise_db()
root = Tk()
root.title('Silty family dashboard')
# make it cover the entire screen
w, h = root.winfo_screenwidth(), root.winfo_screenheight()
root.overrideredirect(1)
root.geometry('%dx%d+0+0' % (w, h))
root.focus_set() # <-- move focus to this widget
root.bind('<Escape>', lambda e: e.widget.quit())
root.config(cursor='none')
#create widgets w/ various styling attributes
title = Label(root,
    text='♥ Silty-Dash ♥',
    font=('Nimbus Sans L', 14),
    bg=colour_1,
    fg=colour_4)
exit = Label(root, text='quit',
    font=('Nimbus Sans L', 14),
    bg=colour_1,
    fg=colour_4)
f_width, f_height=(w / 2.5), (h / 2)
fifth = (w / 5)
front = Frame(root)
clock = Frame(front, height=f_height)
clock.grid_propagate(False)
gallery = Frame(front, width=f_width, height=f_height)
gallery.grid_propagate(False)
left_panel = Frame(front, width=fifth)
left_panel.grid_propagate(False)
note = Frame(front, height=f_height)
note.grid_propagate(False)
stars = Frame(front)
currtime = Label(clock, font=('Nimbus Sans L', 40), bg=colour_2, fg=colour_4)
currdate = Label(clock, font=('Nimbus Sans L', 14), bg=colour_2, fg=colour_4)
left_panel_tabs = Frame(left_panel)
system_tab = Label(left_panel_tabs,
    text='system',
    font=(' Nimbus Mono L', 11),
    anchor=W,
    activebackground=colour_1,
    activeforeground=colour_4,
    background=colour_3,
    foreground=colour_4,
    state=ACTIVE)
processes_tab = Label(left_panel_tabs,
    text='processes',
    font=(' Nimbus Mono L', 11),
    anchor=W,
    activebackground=colour_1,
    activeforeground=colour_4,
    background=colour_3,
    foreground=colour_4)
processes = Frame(left_panel)
system = Frame(left_panel)

nginx = Label(processes)
samba = Label(processes)
mumble = Label(processes)
deluge = Label(processes)

cpu = Label(system)
ram = Label(system)
share_used= Label(system)
share_free= Label(system)
pi_uptime = Label(system)

image_dir = current_path() + '/img'
curly_star = Image.open(image_dir + '/curlystar.png')
curly_star = curly_star.resize((150,150), Image.ANTIALIAS)
silty_star_image = ImageTk.PhotoImage(curly_star)

silty_star = Label(stars, bg=colour_3, fg=colour_5,
image = silty_star_image, anchor=S)
user_list_frame = Frame(stars)
for user in list_users():
    label = Label(user_list_frame, font=('Nimbus Sans L', 12), bg=colour_3,
                fg=colour_5, anchor=N)

note1 = Label(note)
note2 = Label(note)
note3 = Label(note)
note4 = Label(note)
note5 = Label(note)
note6 = Label(note)
note7 = Label(note)


for child in system.winfo_children():
    child.config(padx=10, font=('Nimbus Mono L', 11),
    anchor=W, bg=colour_1, fg=colour_4)
for child in processes.winfo_children():
    child.config(padx=10, font=('Nimbus Mono L', 11), anchor=W)
for child in note.winfo_children():
    child.config(anchor=W, font=('Nimbus Mono L', 11), bg=colour_3, fg=colour_5)

img = Image.open(image_dir + '/img.png')
img = img.resize((220,220), Image.ANTIALIAS)
pic = ImageTk.PhotoImage(img)

panel = Label(gallery, image=pic, bg=colour_1)

# Binding
system_tab.bind('<Button-1>', left_panel_system)
processes_tab.bind('<Button-1>', left_panel_processes)
exit.bind('<Button-1>', close_window)

#Gridding
title.grid(row=0, column=0, sticky=W+E)
exit.grid(row=0, column=1, sticky=W+E)
front.grid(row=1, columnspan = 2, sticky=N+E+W+S)
for child in front.winfo_children():
    child.config(bd = 2, bg='black')

left_panel.grid(row=0, column=0, rowspan=2, sticky=N+E+W+S)

left_panel_tabs.grid(row=0, column=0, sticky=W+E)
system_tab.grid(row=0, column=0, sticky=W+E)
processes_tab.grid(row=0, column=1, sticky=W+E)

system.grid(row=1, column=0, sticky=W+E+N+S)
for child in processes.winfo_children():
    child.grid(sticky=N+E+W+S)

clock.grid(row=0, column=1, sticky=E+W+N+S)
currtime.grid(sticky=N+E+W+S)
currdate.grid(sticky=N+E+W+S)

for child in system.winfo_children():
    child.grid(sticky=N+E+W+S)

stars.grid(row=1, column=2, sticky=N+E+W+S)
for child in stars.winfo_children():
    child.grid(sticky=N+E+W+S)
for child in user_list_frame.winfo_children():
    child.grid(sticky=N+E+W+S)

note.grid(column=2, row=0, sticky=N+E+W+S)
for child in note.winfo_children():
    child.grid(sticky=N+E+W+S)

gallery.grid(row=1, column=1, sticky=N+E+W+S)
panel.grid(sticky=N+E+W+S)

#Weighting

Grid.rowconfigure(root, 0, weight=0)
Grid.rowconfigure(root, 1, weight=1)

Grid.columnconfigure(root, 0, weight=3)
Grid.columnconfigure(root, 1, weight=2)

Grid.columnconfigure(front, 0, weight=1)
Grid.columnconfigure(front, 1, weight=0)
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

Grid.columnconfigure(user_list_frame, 0, weight=1)

Grid.columnconfigure(clock, 0, weight=3)
Grid.rowconfigure(clock, 0, weight=1)
Grid.rowconfigure(clock, 1, weight=1)

Grid.columnconfigure(note, 0, weight=1)
Grid.rowconfigure(note, 0, weight=1)
Grid.rowconfigure(note, 1, weight=1)
Grid.rowconfigure(note, 2, weight=1)
Grid.rowconfigure(note, 3, weight=1)
Grid.rowconfigure(note, 4, weight=1)
Grid.rowconfigure(note, 5, weight=1)
Grid.rowconfigure(note, 6, weight=1)


Grid.rowconfigure(gallery, 0, weight=1)
Grid.columnconfigure(gallery, 0, weight=1)
#finish
root.after(10, system_update)
root.after(10, processes_update)
root.after(10, note_update)
root.after(10, clock_update)
root.after(10, stars_update)
root.after(500, gallery_widget)
root.mainloop()



#testing testin gone two thrww. This keyboard feels like shit man,  how do you cope????????? My left testicke us vufgger than my right one which is pretty stranghe cibsudering I'm a gotl
