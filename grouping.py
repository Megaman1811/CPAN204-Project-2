#!C:\Python\python.exe
import cgi
from random import shuffle

print("Content-type:text/html\r\n\r\n")

formData = cgi.FieldStorage()

with open("data.txt", "r") as file:
    groupSize = int(formData["groupSize"].value)    # Taking the value from the webform
    # groupSize = 2
    groups = {}  # This is the groups
    groupId = []  # This is the people in the groups

    info = file.readlines()
    shuffle(info)  # Shuffles people around to form groups

    for people in info:
        pInfo = people.strip().split(",")
        name = pInfo[0]
        Email = pInfo[1]
        vacationPlan = pInfo[2]
        date = pInfo[3]
        groupId = vacationPlan + date   # I form the id by adding the vacation plan to the date. Ugly but works
        gN = 0  # Group Number
        test = False

        while not test:
            gN = str(gN)    # Required the number to be a string to add it to the group ID
            groupId = groupId + gN

            if groupId in groups:

                if len(groups[groupId]) < groupSize:
                    groups[groupId].append(name)
                    test = True

                else:
                    test = False
                    # I had a weird bug here where it wouldn't increment since it was a string
                    # it worked fine with a hardcoded int for group size but not input from the form
                    gN = int(gN)
                    gN += 1
                    gN = str(gN)

            else:
                groupId + vacationPlan
                groupId + date
                groups[groupId] = [name]
                test = True

print("<html>"  # Making the table now
      "<style>\ntable, th, td {\n  border: 1px solid black;\n</style>"
      "<body>\n<table>\n<tr><th>Group ID</th><th colspan=\"" + str(groupSize) + "\">Group</th>\n</tr>")
for keys, values in groups.items():
    print("<tr><td>" + keys + "</td>")
    for item in values:
        print("<td>" + item + "</td>\n")
