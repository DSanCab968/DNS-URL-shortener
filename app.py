from flask import Flask, redirect, abort
import dns.resolver
import os

DOMAIN = os.getenv("SHORT_DOMAIN", "dscsystems.org")

app = Flask(__name__)

def get_txt_for(subdomain):
    name = f"{subdomain}.{DOMAIN}"
    try:
        answers = dns.resolver.resolve(name, 'TXT')
        for rdata in answers:
            txt_parts = [b.decode() for b in rdata.strings]
            if txt_parts:
                return "".join(txt_parts)
    except Exception:
        return None
    return None

@app.route("/<path:short>", methods=["GET"])
def redirect_short(short):
    key = short.split('/')[0]
    url = get_txt_for(key)
    if not url:
        return abort(404, description="Short not found or DNS TXT missing")

    if not (url.startswith("http://") or url.startswith("https://")):
        return abort(500, description="Invalid target URL in TXT record")

    return redirect(url, code=302)

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=int(os.getenv("PORT", 8000)))
