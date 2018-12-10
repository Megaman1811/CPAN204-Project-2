#!C:\Python\python.exe
import cgi
from random import shuffle
print("Content-type:text/html\r\n\r\n")

formData = cgi.FieldStorage()

with open("data.txt", "r") as file:
    groupSize = formData["groupSize"].value
    groups = {}
    groupIds = []
    info = file.readlines()
    shuffle(info)  # Shuffles people around to form groups

    for people in info:
        pInfo = people.strip().split(",")
        name = pInfo[0]
        Email = pInfo[1]
        vacationPlan = pInfo[2]
        date = pInfo[3]
        groupId = vacationPlan + date
        n = 0
        check = False

        while not check:


