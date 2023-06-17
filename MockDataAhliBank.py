
import random
# this script creates a file with i amount of random records.
# sidenote: the stp is higher than 1030 or lower than 930
createFile = open('MockDataAhliBank.sql', "x") # the name of the file that is created
i = 100000
long = 0    # longitude
lat = 0     # latitude
timestamp = "" # dates are only May or June. and NOT the 31st of May for convenience
statname = ""
cldc = 0    # cloudiness
stp = 0     # airpressure
stpup = 0   # tempvars
stpdown = 0 # tempvars
createFile.write("insert into measurement_examples values")
for x in range(i):
    cldc = round(random.uniform(1.0, 99.9), 1) 
    lat = round(random.uniform(1.0, 99.9), 3)
    long = round(random.uniform(1.0, 99.9), 3)
    statname = "\"station" + str(random.randint(1, 20))+ "\""
    stpup = random.randint(1030, 1099)
    stpdown = random.randint(601, 929)
    upOrDown = random.randint(1,2)
    stp = stpdown
    month = random.randint(5,6)
    day = random.randint(1, 30)
    hour = random.randint(0,23)
    minute = random.randint(0,59)
    seconds= random.randint(0,59)
    timestamp = "\"2023-"+ f"{month:02d}-"+ f"{day:02d} "+ f"{hour:02d}:"+f"{minute:02d}:"+ f"{seconds:02d}"+"\""
    if upOrDown == 1: # decides if stp goes above or below threshhold
        stp = stpup
    
    insertstmt = "( null, "+str(statname)+","+str(timestamp)+', '+str(lat)+', '+str(long)+', '+str(stp)+', '+str(cldc)+'),\n'
    createFile.write(insertstmt)