# DNS URL shortener practice

## Author: Daniel Sánchez Cabello - 2ºASIR B

## Objective

The objective of this practice is to 

## Steps

### 1. Checking DNS in IONOS 

### 1.1 Aiming the domain to our public IP

The first thing we want to do is to have an A record in the domain that aims to our public IP address. To do that, check the public IP in any page dedicated to telling the users their public IPs. Then add the record.

Use sig +short A dscysytems.org and if it returns the IP everything is OK.

### 1.2 TXT record cretion

Create a txt record in IONOS named "test" to try if it works.

hostname → test
type → txt
value → https://google.com
ttl → 300 (5min)

Test with: dig +short TXT test.dscsystems.org

It must return google's address.

## 2. Proyect structure creation

To do this proyect I created a repository with the following files:

-app.py --> the program that manages the URl shortening
-gitignore --> avoid undesired files and folders
-readme.md --> this document where everything is documented
-requirements.txt --> file that will be called by app.py to install dependences

### 2.1 Python environment and dependences management

We must install the following dependencies if they are not installed:

```bash
    sudo apt update
    sudo apt install -y python3 python3-venv python3-pip

```

Now we must create the virtual environment:

```bash
    python3 -m venv venv 
    source venv/bin/activate #environment activation
    pip install -r requirements.txt #requierements installation

```
This must be the content of requierements:

```ini
    Flask==2.3.0
    dnspython==2.4.2
    gunicorn==20.1.0
```
Now, export the environmental variable and run the python program:

```bash
    export SHORT_DOMAIN=dscsystems.org
    python app.py
``` 

Try locally:

```bash
    #Execute this command
    curl -I http://localhost:8000/test
    #This is the output
    HTTP/1.1 302 FOUND
    Server: Werkzeug/3.1.3 Python/3.13.5
    Date: Tue, 04 Nov 2025 21:10:36 GMT
    Content-Type: text/html; charset=utf-8
    Content-Length: 223
    Location: https://google.com
    Connection: close
```
