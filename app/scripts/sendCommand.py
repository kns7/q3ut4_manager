import socket
import argparse

class RCON:

    def __init__(self, ip, password, port=27960):
        self.ip = ip
        self.port = port
        self.password = password
        self.prefix = bytes([0xff, 0xff, 0xff, 0xff]) + b'rcon '
        self.socket = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)

    def send_command(self, command, response=True):

        cmd = f"{self.password} {command}".encode()
        query = self.prefix + cmd

        self.socket.connect((self.ip, self.port))
        self.socket.send(query)

        if response:
            self.socket.settimeout(3)
            try:
                data = self.socket.recv(4096)
                return data
            except socket.timeout:
                return None

if __name__ == "__main__":
    # Parse arguments
    parser = argparse.ArgumentParser()
    parser.add_argument("-h","--host", help="Server Hostname or IP Address")
    parser.add_argument("-p","--pass", help="RCON Password")
    parser.add_argument("command", help="RCON Command")
    args = parser.parse_args()

    rcon = RCON(args.host, args.pass)

    response =  rcon.send_command(args.command)
    print(response)