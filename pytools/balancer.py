import paramiko, os, socket, time, json, re
from config import decrypt
from urllib import urlopen

# Status: 0 - Not Started       1 - Started         2 - Done

def getIP():
    try:
        data = str(urlopen('http://checkip.dyndns.com/').read())
        return re.compile(r'Address: (\d+\.\d+\.\d+\.\d+)').search(data).group(1)
    except: return None

rIbalancer = "https://xtreamtools.org/XCodes/installBalancer.py"
rBreload = "https://xtreamtools.org/XCodes/fbreload.py"
rFbremake = "https://xtreamtools.org/XCodes/fbremake.py"
rFsremake = "/home/xtreamcodes/iptv_xtream_codes/pytools/fsremake.py"
rSreload = "/home/xtreamcodes/iptv_xtream_codes/pytools/sreload.py"
rUgeolite2 = "https://xtreamtools.org/XCodes/updategeolite.py"
rYoutube2 = "https://xtreamtools.org/XCodes/updateyoutube.py"
rUrelease = "/home/xtreamcodes/iptv_xtream_codes/pytools/urelease.py"
rTrova = "/home/xtreamcodes/iptv_xtream_codes/adtools/balancer/"
rConfig = decrypt()
rIP = getIP()
rIPlocal = "127.0.0.1"
rTime = time.time()

def writeDetails(rDetails):
    rFile = open("%s%d.json" % (rTrova, int(rDetails["id"])), "w")
    rFile.write(json.dumps(rDetails))
    rFile.close()

def installBalancer(rDetails):
    rDetails["status"] = 1
    writeDetails(rDetails)
    rClient = paramiko.SSHClient()
    rClient.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try: rClient.connect(rDetails["host"], rDetails["port"], "root", rDetails["password"])
    except:
        rDetails["status"] = 0
        writeDetails(rDetails)
        return True
    try:
        rIn, rOut, rErr = rClient.exec_command("sudo apt-get install python -y")
        rStatus = rOut.channel.recv_exit_status()
        rIn, rOut, rErr = rClient.exec_command("sudo wget -q \"%s\" -O \"/tmp/Ibalancer.py\"" % rIbalancer)
        rStatus = rOut.channel.recv_exit_status()
        rIn, rOut, rErr = rClient.exec_command("sudo python /tmp/Ibalancer.py \"%s\" \"%s\" \"%s\" \"%s\" \"%s\" %d %d %d %d" % (rIP, rConfig["db_port"], rConfig["db_user"], rConfig["db_pass"], rConfig["db_name"], int(rDetails["id"]), int(rDetails["http_broadcast_port"]), int(rDetails["https_broadcast_port"]), int(rDetails["rtmp_port"])))
        rStatus = rOut.channel.recv_exit_status()
    except: pass
    rDetails["status"] = 2
    try: os.remove("%s%d.json" % (rTrova, int(rDetails["id"])))
    except: writeDetails(rDetails)
    return True

def restartServices(rDetails):
    rClient = paramiko.SSHClient()
    rClient.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try: rClient.connect(rDetails["host"], rDetails["port"], "root", rDetails["password"])
    except: return False
    rClient.exec_command("sudo /home/xtreamcodes/iptv_xtream_codes/start_services.sh")
    try: os.remove("%s%d.json" % (rTrova, int(rDetails["id"])))
    except: pass
    return True

def rebootServer(rDetails):
    rClient = paramiko.SSHClient()
    rClient.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try: rClient.connect(rDetails["host"], rDetails["port"], "root", rDetails["password"])
    except: return False
    rClient.exec_command("sudo reboot")
    try: os.remove("%s%d.json" % (rTrova, int(rDetails["id"])))
    except: pass
    return True
    
def fbReload(rDetails):
    rDetails["status"] = 1
    writeDetails(rDetails)
    rClient = paramiko.SSHClient()
    rClient.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try: rClient.connect(rDetails["host"], rDetails["port"], "root", rDetails["password"])
    except:
        rDetails["status"] = 0
        writeDetails(rDetails)
        return True
    try:
        rIn, rOut, rErr = rClient.exec_command("sudo apt-get install python -y")
        rStatus = rOut.channel.recv_exit_status()
        rIn, rOut, rErr = rClient.exec_command("sudo wget -q \"%s\" -O \"/tmp/breload.py\"" % rBreload)
        rStatus = rOut.channel.recv_exit_status()
        rIn, rOut, rErr = rClient.exec_command("sudo python /tmp/breload.py \"%s\" \"%s\" \"%s\" \"%s\" \"%s\" %d" % (rIP, rConfig["db_port"], rConfig["db_user"], rConfig["db_pass"], rConfig["db_name"], int(rDetails["id"])))
        rStatus = rOut.channel.recv_exit_status()
    except: pass
    rDetails["status"] = 2
    try: os.remove("%s%d.json" % (rTrova, int(rDetails["id"])))
    except: writeDetails(rDetails)
    return True

def fsReload(rDetails):
    rClient = paramiko.SSHClient()
    rClient.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try: rClient.connect(rDetails["host"], rDetails["port"], "root", rDetails["password"])
    except: return False
    try:
        rClient.exec_command("sudo cp \"%s\" \"/tmp/sreload.py\"" % rSreload)
        rClient.exec_command("sudo python /tmp/sreload.py \"%s\" \"%s\" \"%s\" \"%s\" \"%s\" %d" % (rIPlocal, rConfig["db_port"], rConfig["db_user"], rConfig["db_pass"], rConfig["db_name"], int(rDetails["id"])))
    except: pass
    try: os.remove("%s%d.json" % (rTrova, int(rDetails["id"])))
    except: pass
    return True    

def fbRemake(rDetails):
    rDetails["status"] = 1
    writeDetails(rDetails)
    rClient = paramiko.SSHClient()
    rClient.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try: rClient.connect(rDetails["host"], rDetails["port"], "root", rDetails["password"])
    except:
        rDetails["status"] = 0
        writeDetails(rDetails)
        return True
    try:
        rIn, rOut, rErr = rClient.exec_command("sudo apt-get install python -y")
        rStatus = rOut.channel.recv_exit_status()
        rIn, rOut, rErr = rClient.exec_command("sudo wget -q \"%s\" -O \"/tmp/fbremake.py\"" % rFbremake)
        rStatus = rOut.channel.recv_exit_status()
        rIn, rOut, rErr = rClient.exec_command("sudo python /tmp/fbremake.py \"%s\" \"%s\" \"%s\" \"%s\" \"%s\" %d" % (rIP, rConfig["db_port"], rConfig["db_user"], rConfig["db_pass"], rConfig["db_name"], int(rDetails["id"])))
        rStatus = rOut.channel.recv_exit_status()
    except: pass
    rDetails["status"] = 2
    try: os.remove("%s%d.json" % (rTrova, int(rDetails["id"])))
    except: writeDetails(rDetails)
    return True
    
def fsRemake(rDetails):
    rClient = paramiko.SSHClient()
    rClient.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try: rClient.connect(rDetails["host"], rDetails["port"], "root", rDetails["password"])
    except: return False
    try:
        rClient.exec_command("sudo cp \"%s\" \"/tmp/fsremake.py\"" % rFsremake)
        rClient.exec_command("sudo python /tmp/fsremake.py \"%s\" \"%s\" \"%s\" \"%s\" \"%s\" %d" % (rIPlocal, rConfig["db_port"], rConfig["db_user"], rConfig["db_pass"], rConfig["db_name"], int(rDetails["id"])))
    except: pass
    try: os.remove("%s%d.json" % (rTrova, int(rDetails["id"])))
    except: pass
    return True

def Ugeolite(rDetails):
    rDetails["status"] = 1
    writeDetails(rDetails)
    rClient = paramiko.SSHClient()
    rClient.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try: rClient.connect(rDetails["host"], rDetails["port"], "root", rDetails["password"])
    except:
        rDetails["status"] = 0
        writeDetails(rDetails)
        return True
    try:
        rIn, rOut, rErr = rClient.exec_command("sudo apt-get install python -y")
        rStatus = rOut.channel.recv_exit_status()
        rIn, rOut, rErr = rClient.exec_command("sudo wget -q \"%s\" -O \"/tmp/ugeolite.py\"" % rUgeolite2)
        rStatus = rOut.channel.recv_exit_status()
        rIn, rOut, rErr = rClient.exec_command("sudo python /tmp/ugeolite.py \"%s\" \"%s\" \"%s\" \"%s\" \"%s\" %d" % (rIP, rConfig["db_port"], rConfig["db_user"], rConfig["db_pass"], rConfig["db_name"], int(rDetails["id"])))
        rStatus = rOut.channel.recv_exit_status()
    except: pass
    rDetails["status"] = 2
    try: os.remove("%s%d.json" % (rTrova, int(rDetails["id"])))
    except: writeDetails(rDetails)
    return True
    
def Uyoutube(rDetails):
    rDetails["status"] = 1
    writeDetails(rDetails)
    rClient = paramiko.SSHClient()
    rClient.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try: rClient.connect(rDetails["host"], rDetails["port"], "root", rDetails["password"])
    except:
        rDetails["status"] = 0
        writeDetails(rDetails)
        return True
    try:
        rIn, rOut, rErr = rClient.exec_command("sudo apt-get install python -y")
        rStatus = rOut.channel.recv_exit_status()
        rIn, rOut, rErr = rClient.exec_command("sudo wget -q \"%s\" -O \"/tmp/uyoutube.py\"" % rYoutube2)
        rStatus = rOut.channel.recv_exit_status()
        rIn, rOut, rErr = rClient.exec_command("sudo python /tmp/uyoutube.py \"%s\" \"%s\" \"%s\" \"%s\" \"%s\" %d" % (rIP, rConfig["db_port"], rConfig["db_user"], rConfig["db_pass"], rConfig["db_name"], int(rDetails["id"])))
        rStatus = rOut.channel.recv_exit_status()
    except: pass
    rDetails["status"] = 2
    try: os.remove("%s%d.json" % (rTrova, int(rDetails["id"])))
    except: writeDetails(rDetails)
    return True    
    
def Urelease(rDetails):
    rClient = paramiko.SSHClient()
    rClient.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try: rClient.connect(rDetails["host"], rDetails["port"], "root", rDetails["password"])
    except: return False
    try:
        rClient.exec_command("sudo cp \"%s\" \"/tmp/urelease.py\"" % rUrelease)
        rClient.exec_command("sudo python /tmp/urelease.py \"%s\" \"%s\" \"%s\" \"%s\" \"%s\" %d" % (rIPlocal, rConfig["db_port"], rConfig["db_user"], rConfig["db_pass"], rConfig["db_name"], int(rDetails["id"])))
    except: pass
    try: os.remove("%s%d.json" % (rTrova, int(rDetails["id"])))
    except: pass
    return True
    
if __name__ == "__main__":
    if rIP and rIPlocal and rConfig:
        for rFile in os.listdir(rTrova):
            try: rDetails = json.loads(open(rTrova + rFile).read())
            except: rDetails = {"status": -1}
            try: rType = rDetails["type"]
            except: rType = None
            if rType == "restart": restartServices(rDetails)
            elif rType == "reboot": rebootServer(rDetails)
            elif rType == "breload": fbReload(rDetails)
            elif rType == "sreload": fsReload(rDetails)
            elif rType == "fbremake": fbRemake(rDetails)
            elif rType == "fsremake": fsRemake(rDetails)
            elif rType == "ugeolite": Ugeolite(rDetails)
            elif rType == "uyoutube": Uyoutube(rDetails)
            elif rType == "urelease": Urelease(rDetails)
            else:
                if rDetails["status"] == 0: installBalancer(rDetails)