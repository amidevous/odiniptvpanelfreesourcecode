#!/usr/bin/env python3.10
# -*- coding: utf-8 -*-
#
# sudo wget -O /root/install.py3  https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/install.py3 && && sudo python3.10 /root/install.py3
#
import os
# os.system('pip install requests >/dev/null')
# import requests
from urllib.request import Request, urlopen
import random
import shutil
import socket
import string
import subprocess
import sys
import time
import json
import base64
# import urllib2
from itertools import cycle, zip_longest as izip
from itertools import zip_longest
from datetime import datetime
rDownloadURL = {"main": "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/install-bin-main.sh",
                "sub": "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/install-bin-sub.sh"}
rInstall = {"MAIN": "main", "LB": "sub"}
eMySQLenc = "IyBYdHJlYW0gQ29kZXMKCltjbGllbnRdCnBvcnQgICAgICAgICAgICA9IDMzMDYKCltteXNxbGRfc2FmZV0KbmljZSAgICAgICAgICAgID0gMAoKW215c3FsZF0KdXNlciAgICAgICAgICAgID0gbXlzcWwKcG9ydCAgICAgICAgICAgID0gNzk5OQpiYXNlZGlyICAgICAgICAgPSAvdXNyCmRhdGFkaXIgICAgICAgICA9IC92YXIvbGliL215c3FsCnRtcGRpciAgICAgICAgICA9IC90bXAKbGMtbWVzc2FnZXMtZGlyID0gL3Vzci9zaGFyZS9teXNxbApza2lwLWV4dGVybmFsLWxvY2tpbmcKc2tpcC1uYW1lLXJlc29sdmU9MQoKYmluZC1hZGRyZXNzICAgICAgICAgICAgPSAqCmtleV9idWZmZXJfc2l6ZSA9IDEyOE0KCm15aXNhbV9zb3J0X2J1ZmZlcl9zaXplID0gNE0KbWF4X2FsbG93ZWRfcGFja2V0ICAgICAgPSA2NE0KbXlpc2FtLXJlY292ZXItb3B0aW9ucyA9IEJBQ0tVUAptYXhfbGVuZ3RoX2Zvcl9zb3J0X2RhdGEgPSA4MTkyCnF1ZXJ5X2NhY2hlX2xpbWl0ICAgICAgID0gNE0KcXVlcnlfY2FjaGVfc2l6ZSAgICAgICAgPSAyNTZNCgoKZXhwaXJlX2xvZ3NfZGF5cyAgICAgICAgPSAxMAptYXhfYmlubG9nX3NpemUgICAgICAgICA9IDEwME0KCm1heF9jb25uZWN0aW9ucyAgPSAyMDAwMApiYWNrX2xvZyA9IDQwOTYKb3Blbl9maWxlc19saW1pdCA9IDIwMjQwCmlubm9kYl9vcGVuX2ZpbGVzID0gMjAyNDAKbWF4X2Nvbm5lY3RfZXJyb3JzID0gMzA3Mgp0YWJsZV9vcGVuX2NhY2hlID0gNDA5Ngp0YWJsZV9kZWZpbml0aW9uX2NhY2hlID0gNDA5NgoKCnRtcF90YWJsZV9zaXplID0gMUcKbWF4X2hlYXBfdGFibGVfc2l6ZSA9IDFHCgppbm5vZGJfYnVmZmVyX3Bvb2xfc2l6ZSA9IDEwRwppbm5vZGJfYnVmZmVyX3Bvb2xfaW5zdGFuY2VzID0gMTAKaW5ub2RiX3JlYWRfaW9fdGhyZWFkcyA9IDY0Cmlubm9kYl93cml0ZV9pb190aHJlYWRzID0gNjQKaW5ub2RiX3RocmVhZF9jb25jdXJyZW5jeSA9IDAKaW5ub2RiX2ZsdXNoX2xvZ19hdF90cnhfY29tbWl0ID0gMAppbm5vZGJfZmx1c2hfbWV0aG9kID0gT19ESVJFQ1QKcGVyZm9ybWFuY2Vfc2NoZW1hID0gMAppbm5vZGItZmlsZS1wZXItdGFibGUgPSAxCmlubm9kYl9pb19jYXBhY2l0eT0yMDAwMAppbm5vZGJfdGFibGVfbG9ja3MgPSAwCmlubm9kYl9sb2NrX3dhaXRfdGltZW91dCA9IDAKI2lubm9kYl9kZWFkbG9ja19kZXRlY3QgPSAwCgoKc3FsLW1vZGU9Ik5PX0VOR0lORV9TVUJTVElUVVRJT04iCgpbbXlzcWxkdW1wXQpxdWljawpxdW90ZS1uYW1lcwptYXhfYWxsb3dlZF9wYWNrZXQgICAgICA9IDE2TQoKW215c3FsXQoKW2lzYW1jaGtdCmtleV9idWZmZXJfc2l6ZSAgICAgICAgICAgICAgPSAxNk0="
rMySQLCnf = base64.b64decode(eMySQLenc).decode('utf-8')
eMySQLINITenc = "IyEvYmluL3NoCiMgQ29weXJpZ2h0IEFiYW5kb25lZCAxOTk2IFRDWCBEYXRhS29uc3VsdCBBQiAmIE1vbnR5IFByb2dyYW0gS0IgJiBEZXRyb24gSEIKIyBUaGlzIGZpbGUgaXMgcHVibGljIGRvbWFpbiBhbmQgY29tZXMgd2l0aCBOTyBXQVJSQU5UWSBvZiBhbnkga2luZAoKIyBNYXJpYURCIGRhZW1vbiBzdGFydC9zdG9wIHNjcmlwdC4KCiMgVXN1YWxseSB0aGlzIGlzIHB1dCBpbiAvZXRjL2luaXQuZCAoYXQgbGVhc3Qgb24gbWFjaGluZXMgU1lTViBSNCBiYXNlZAojIHN5c3RlbXMpIGFuZCBsaW5rZWQgdG8gL2V0Yy9yYzMuZC9TOTlteXNxbCBhbmQgL2V0Yy9yYzAuZC9LMDFteXNxbC4KIyBXaGVuIHRoaXMgaXMgZG9uZSB0aGUgbXlzcWwgc2VydmVyIHdpbGwgYmUgc3RhcnRlZCB3aGVuIHRoZSBtYWNoaW5lIGlzCiMgc3RhcnRlZCBhbmQgc2h1dCBkb3duIHdoZW4gdGhlIHN5c3RlbXMgZ29lcyBkb3duLgoKIyBDb21tZW50cyB0byBzdXBwb3J0IGNoa2NvbmZpZyBvbiBSZWQgSGF0IExpbnV4CiMgY2hrY29uZmlnOiAyMzQ1IDY0IDM2CiMgZGVzY3JpcHRpb246IEEgdmVyeSBmYXN0IGFuZCByZWxpYWJsZSBTUUwgZGF0YWJhc2UgZW5naW5lLgoKIyBDb21tZW50cyB0byBzdXBwb3J0IExTQiBpbml0IHNjcmlwdCBjb252ZW50aW9ucwojIyMgQkVHSU4gSU5JVCBJTkZPCiMgUHJvdmlkZXM6IG15c3FsCiMgUmVxdWlyZWQtU3RhcnQ6ICRsb2NhbF9mcyAkbmV0d29yayAkcmVtb3RlX2ZzCiMgU2hvdWxkLVN0YXJ0OiB5cGJpbmQgbnNjZCBsZGFwIG50cGQgeG50cGQKIyBSZXF1aXJlZC1TdG9wOiAkbG9jYWxfZnMgJG5ldHdvcmsgJHJlbW90ZV9mcwojIERlZmF1bHQtU3RhcnQ6ICAyIDMgNCA1CiMgRGVmYXVsdC1TdG9wOiAwIDEgNgojIFNob3J0LURlc2NyaXB0aW9uOiBzdGFydCBhbmQgc3RvcCBNYXJpYURCCiMgRGVzY3JpcHRpb246IE1hcmlhREIgaXMgYSB2ZXJ5IGZhc3QgYW5kIHJlbGlhYmxlIFNRTCBkYXRhYmFzZSBlbmdpbmUuCiMjIyBFTkQgSU5JVCBJTkZPCgojIGhhdmUgdG8gZG8gb25lIG9mIHRoZSBmb2xsb3dpbmcgdGhpbmdzIGZvciB0aGlzIHNjcmlwdCB0byB3b3JrOgojCiMgLSBSdW4gdGhpcyBzY3JpcHQgZnJvbSB3aXRoaW4gdGhlIE1hcmlhREIgaW5zdGFsbGF0aW9uIGRpcmVjdG9yeQojIC0gQ3JlYXRlIGEgL2V0Yy9teS5jbmYgZmlsZSB3aXRoIHRoZSBmb2xsb3dpbmcgaW5mb3JtYXRpb246CiMgICBbbXlzcWxkXQojICAgYmFzZWRpcj08cGF0aC10by1teXNxbC1pbnN0YWxsYXRpb24tZGlyZWN0b3J5PgojIC0gQWRkIHRoZSBhYm92ZSB0byBhbnkgb3RoZXIgY29uZmlndXJhdGlvbiBmaWxlIChmb3IgZXhhbXBsZSB+Ly5teS5pbmkpCiMgICBhbmQgY29weSBteV9wcmludF9kZWZhdWx0cyB0byAvdXNyL2JpbgojIC0gQWRkIHRoZSBwYXRoIHRvIHRoZSBteXNxbC1pbnN0YWxsYXRpb24tZGlyZWN0b3J5IHRvIHRoZSBiYXNlZGlyIHZhcmlhYmxlCiMgICBiZWxvdy4KIwojIElmIHlvdSB3YW50IHRvIGFmZmVjdCBvdGhlciBNYXJpYURCIHZhcmlhYmxlcywgeW91IHNob3VsZCBtYWtlIHlvdXIgY2hhbmdlcwojIGluIHRoZSAvZXRjL215LmNuZiwgfi8ubXkuY25mIG9yIG90aGVyIE1hcmlhREIgY29uZmlndXJhdGlvbiBmaWxlcy4KCiMgSWYgeW91IGNoYW5nZSBiYXNlIGRpciwgeW91IG11c3QgYWxzbyBjaGFuZ2UgZGF0YWRpci4gVGhlc2UgbWF5IGdldAojIG92ZXJ3cml0dGVuIGJ5IHNldHRpbmdzIGluIHRoZSBNYXJpYURCIGNvbmZpZ3VyYXRpb24gZmlsZXMuCgpiYXNlZGlyPQpkYXRhZGlyPQoKIyBEZWZhdWx0IHZhbHVlLCBpbiBzZWNvbmRzLCBhZnRlcndoaWNoIHRoZSBzY3JpcHQgc2hvdWxkIHRpbWVvdXQgd2FpdGluZwojIGZvciBzZXJ2ZXIgc3RhcnQuCiMgVmFsdWUgaGVyZSBpcyBvdmVycmlkZGVuIGJ5IHZhbHVlIGluIG15LmNuZi4KIyAwIG1lYW5zIGRvbid0IHdhaXQgYXQgYWxsCiMgTmVnYXRpdmUgbnVtYmVycyBtZWFuIHRvIHdhaXQgaW5kZWZpbml0ZWx5CnNlcnZpY2Vfc3RhcnR1cF90aW1lb3V0PTkwMAoKIyBMb2NrIGRpcmVjdG9yeSBmb3IgUmVkIEhhdCAvIFN1U0UuCmxvY2tkaXI9Jy92YXIvbG9jay9zdWJzeXMnCmxvY2tfZmlsZV9wYXRoPSIkbG9ja2Rpci9teXNxbCIKCiMgVGhlIGZvbGxvd2luZyB2YXJpYWJsZXMgYXJlIG9ubHkgc2V0IGZvciBsZXR0aW5nIG15c3FsLnNlcnZlciBmaW5kIHRoaW5ncy4KCiMgU2V0IHNvbWUgZGVmYXVsdHMKbWFyaWFkYmRfcGlkX2ZpbGVfcGF0aD0KaWYgdGVzdCAteiAiJGJhc2VkaXIiCnRoZW4KICBiYXNlZGlyPUBwcmVmaXhACiAgYmluZGlyPUBiaW5kaXJACiAgaWYgdGVzdCAteiAiJGRhdGFkaXIiCiAgdGhlbgogICAgZGF0YWRpcj1AbG9jYWxzdGF0ZWRpckAKICBmaQogIHNiaW5kaXI9QHNiaW5kaXJACiAgbGliZXhlY2Rpcj1AbGliZXhlY2RpckAKZWxzZQogIGJpbmRpcj0iJGJhc2VkaXIvYmluIgogIGlmIHRlc3QgLXogIiRkYXRhZGlyIgogIHRoZW4KICAgIGRhdGFkaXI9IiRiYXNlZGlyL2RhdGEiCiAgZmkKICBzYmluZGlyPSIkYmFzZWRpci9zYmluIgogIGlmIHRlc3QgLWYgIiRiYXNlZGlyL2Jpbi9tYXJpYWRiZCIKICB0aGVuCiAgICBsaWJleGVjZGlyPSIkYmFzZWRpci9iaW4iCiAgZWxzZQogICAgbGliZXhlY2Rpcj0iJGJhc2VkaXIvbGliZXhlYyIKICBmaQpmaQoKIyBkYXRhZGlyX3NldCBpcyB1c2VkIHRvIGRldGVybWluZSBpZiBkYXRhZGlyIHdhcyBzZXQgKGFuZCBzbyBzaG91bGQgYmUKIyAqbm90KiBzZXQgaW5zaWRlIG9mIHRoZSAtLWJhc2VkaXI9IGhhbmRsZXIuKQpkYXRhZGlyX3NldD0KCiMKIyBVc2UgTFNCIGluaXQgc2NyaXB0IGZ1bmN0aW9ucyBmb3IgcHJpbnRpbmcgbWVzc2FnZXMsIGlmIHBvc3NpYmxlCiMgSW5jbHVkZSBub24tTFNCIFJlZCBIYXQgaW5pdCBmdW5jdGlvbnMgdG8gbWFrZSBzeXN0ZW1jdGwgcmVkaXJlY3Qgd29yawppbml0X2Z1bmN0aW9ucz0iL2V0Yy9pbml0LmQvZnVuY3Rpb25zIgpsc2JfZnVuY3Rpb25zPSIvbGliL2xzYi9pbml0LWZ1bmN0aW9ucyIKaWYgdGVzdCAtZiAkbHNiX2Z1bmN0aW9uczsgdGhlbgogIC4gJGxzYl9mdW5jdGlvbnMKZmkKCmlmIHRlc3QgLWYgJGluaXRfZnVuY3Rpb25zOyB0aGVuCiAgLiAkaW5pdF9mdW5jdGlvbnMKZWxzZQogIGVjaG8gIm9rIgogICNsb2dfc3VjY2Vzc19tc2coKQogIHsKICAgIGVjaG8gIiBTVUNDRVNTISAkQCIKICB9CiAgbG9nX2ZhaWx1cmVfbXNnKCkKICB7CiAgICBlY2hvICIgRVJST1IhICRAIgogIH0KZmkKClBBVEg9Ii9zYmluOi91c3Ivc2JpbjovYmluOi91c3IvYmluOiRiYXNlZGlyL2JpbiIKZXhwb3J0IFBBVEgKCm1vZGU9JDEgICAgIyBzdGFydCBvciBzdG9wCgpbICQjIC1nZSAxIF0gJiYgc2hpZnQKCmNhc2UgYGVjaG8gInRlc3RpbmdcYyJgLGBlY2hvIC1uIHRlc3RpbmdgIGluCiAgICAqYyosLW4qKSBlY2hvX249ICAgZWNob19jPSAgICAgOzsKICAgICpjKiwqKSAgIGVjaG9fbj0tbiBlY2hvX2M9ICAgICA7OwogICAgKikgICAgICAgZWNob19uPSAgIGVjaG9fYz0nXGMnIDs7CmVzYWMKCnBhcnNlX3NlcnZlcl9hcmd1bWVudHMoKSB7CiAgZm9yIGFyZyBkbwogICAgdmFsPWBlY2hvICIkYXJnIiB8IHNlZCAtZSAncy9eW149XSo9Ly8nYAogICAgY2FzZSAiJGFyZyIgaW4KICAgICAgLS1iYXNlZGlyPSopICBiYXNlZGlyPSIkdmFsIgogICAgICAgICAgICAgICAgICAgIGJpbmRpcj0iJGJhc2VkaXIvYmluIgoJCSAgICBpZiB0ZXN0IC16ICIkZGF0YWRpcl9zZXQiOyB0aGVuCgkJICAgICAgZGF0YWRpcj0iJGJhc2VkaXIvZGF0YSIKCQkgICAgZmkKCQkgICAgc2JpbmRpcj0iJGJhc2VkaXIvc2JpbiIKICAgICAgICAgICAgICAgICAgICBpZiB0ZXN0IC1mICIkYmFzZWRpci9iaW4vbWFyaWFkYmQiCiAgICAgICAgICAgICAgICAgICAgdGhlbgogICAgICAgICAgICAgICAgICAgICAgbGliZXhlY2Rpcj0iJGJhc2VkaXIvYmluIgogICAgICAgICAgICAgICAgICAgIGVsc2UKICAgICAgICAgICAgICAgICAgICAgIGxpYmV4ZWNkaXI9IiRiYXNlZGlyL2xpYmV4ZWMiCiAgICAgICAgICAgICAgICAgICAgZmkKCQkgICAgbGliZXhlY2Rpcj0iJGJhc2VkaXIvbGliZXhlYyIKICAgICAgICA7OwogICAgICAtLWRhdGFkaXI9KikgIGRhdGFkaXI9IiR2YWwiCgkJICAgIGRhdGFkaXJfc2V0PTEKCTs7CiAgICAgIC0tbG9nLWJhc2VuYW1lPSp8LS1ob3N0bmFtZT0qfC0tbG9vc2UtbG9nLWJhc2VuYW1lPSopCiAgICAgICAgbWFyaWFkYmRfcGlkX2ZpbGVfcGF0aD0iJHZhbC5waWQiCgk7OwogICAgICAtLXBpZC1maWxlPSopIG1hcmlhZGJkX3BpZF9maWxlX3BhdGg9IiR2YWwiIDs7CiAgICAgIC0tc2VydmljZS1zdGFydHVwLXRpbWVvdXQ9Kikgc2VydmljZV9zdGFydHVwX3RpbWVvdXQ9IiR2YWwiIDs7CiAgICAgIC0tdXNlcj0qKSB1c2VyPSIkdmFsIjsgOzsKICAgIGVzYWMKICBkb25lCn0KCiMgR2V0IGFyZ3VtZW50cyBmcm9tIHRoZSBteS5jbmYgZmlsZSwKIyB0aGUgb25seSBncm91cCwgd2hpY2ggaXMgcmVhZCBmcm9tIG5vdyBvbiBpcyBbbXlzcWxkXQppZiB0ZXN0IC14ICIkYmluZGlyL215X3ByaW50X2RlZmF1bHRzIjsgIHRoZW4KICBwcmludF9kZWZhdWx0cz0iJGJpbmRpci9teV9wcmludF9kZWZhdWx0cyIKZWxzZQogICMgVHJ5IHRvIGZpbmQgYmFzZWRpciBpbiAvZXRjL215LmNuZgogIGNvbmY9L2V0Yy9teS5jbmYKICBwcmludF9kZWZhdWx0cz0KICBpZiB0ZXN0IC1yICRjb25mCiAgdGhlbgogICAgc3VicGF0PSdeW149XSpiYXNlZGlyW149XSo9XCguKlwpJCcKICAgIGRpcnM9YHNlZCAtZSAiLyRzdWJwYXQvIWQiIC1lICdzLy9cMS8nICRjb25mYAogICAgZm9yIGQgaW4gJGRpcnMKICAgIGRvCiAgICAgIGQ9YGVjaG8gJGQgfCBzZWQgLWUgJ3MvWyAJXS8vZydgCiAgICAgIGlmIHRlc3QgLXggIiRkL2Jpbi9teV9wcmludF9kZWZhdWx0cyIKICAgICAgdGhlbgogICAgICAgIHByaW50X2RlZmF1bHRzPSIkZC9iaW4vbXlfcHJpbnRfZGVmYXVsdHMiCiAgICAgICAgYnJlYWsKICAgICAgZmkKICAgIGRvbmUKICBmaQoKICAjIEhvcGUgaXQncyBpbiB0aGUgUEFUSCAuLi4gYnV0IEkgZG91YnQgaXQKICB0ZXN0IC16ICIkcHJpbnRfZGVmYXVsdHMiICYmIHByaW50X2RlZmF1bHRzPSJteV9wcmludF9kZWZhdWx0cyIKZmkKCnVzZXI9J0BNWVNRTERfVVNFUkAnCgpzdV9raWxsKCkgewogIGlmIHRlc3QgIiRVU0VSIiA9ICIkdXNlciI7IHRoZW4KICAgIGtpbGwgJCogPi9kZXYvbnVsbCAyPiYxCiAgZWxzZQogICAgc3UgLSAkdXNlciAtcyAvYmluL3NoIC1jICJraWxsICQqIiA+L2Rldi9udWxsIDI+JjEKICBmaQp9CgojCiMgUmVhZCBkZWZhdWx0cyBmaWxlIGZyb20gJ2Jhc2VkaXInLiAgIElmIHRoZXJlIGlzIG5vIGRlZmF1bHRzIGZpbGUgdGhlcmUKIyBjaGVjayBpZiBpdCdzIGluIHRoZSBvbGQgKGRlcHJpY2F0ZWQpIHBsYWNlIChkYXRhZGlyKSBhbmQgcmVhZCBpdCBmcm9tIHRoZXJlCiMKCmV4dHJhX2FyZ3M9IiIKaWYgdGVzdCAtciAiJGJhc2VkaXIvbXkuY25mIgp0aGVuCiAgZXh0cmFfYXJncz0iLS1kZWZhdWx0cy1leHRyYS1maWxlPSAkYmFzZWRpci9teS5jbmYiCmVsc2UKICBpZiB0ZXN0IC1yICIkZGF0YWRpci9teS5jbmYiCiAgdGhlbgogICAgZXh0cmFfYXJncz0iLS1kZWZhdWx0cy1leHRyYS1maWxlPSAkZGF0YWRpci9teS5jbmYiCiAgZmkKZmkKCnBhcnNlX3NlcnZlcl9hcmd1bWVudHMgYCRwcmludF9kZWZhdWx0cyAkZXh0cmFfYXJncyAtLW15c3FsZCBteXNxbC5zZXJ2ZXJgCnBhcnNlX3NlcnZlcl9hcmd1bWVudHMgIiRAIgoKIyB3YWl0IGZvciB0aGUgcGlkIGZpbGUgdG8gZGlzYXBwZWFyCndhaXRfZm9yX2dvbmUgKCkgewogIHBpZD0iJDEiICAgICAgICAgICAjIHByb2Nlc3MgSUQgb2YgdGhlIHByb2dyYW0gb3BlcmF0aW5nIG9uIHRoZSBwaWQtZmlsZQogIHBpZF9maWxlX3BhdGg9IiQyIiAjIHBhdGggdG8gdGhlIFBJRCBmaWxlLgoKICBpPTAKICBjcmFzaF9wcm90ZWN0aW9uPSJieSBjaGVja2luZyBhZ2FpbiIKCiAgd2hpbGUgdGVzdCAkaSAtbmUgJHNlcnZpY2Vfc3RhcnR1cF90aW1lb3V0IDsgZG8KCiAgICBpZiBzdV9raWxsIC0wICIkcGlkIiA7IHRoZW4KICAgICAgOiAgIyB0aGUgc2VydmVyIHN0aWxsIHJ1bnMKICAgIGVsc2UKICAgICAgaWYgdGVzdCAhIC1zICIkcGlkX2ZpbGVfcGF0aCI7IHRoZW4KICAgICAgICAjIG5vIHNlcnZlciBwcm9jZXNzIGFuZCBubyBwaWQtZmlsZT8gZ3JlYXQsIHdlJ3JlIGRvbmUhCgkJZWNobyAib2siCiAgICAgICAgI2xvZ19zdWNjZXNzX21zZwogICAgICAgIHJldHVybiAwCiAgICAgIGZpCgogICAgICAjIHBpZC1maWxlIGV4aXN0cywgdGhlIHNlcnZlciBwcm9jZXNzIGRvZXNuJ3QuCiAgICAgICMgaXQgbXVzdCd2ZSBjcmFzaGVkLCBhbmQgbXlzcWxkX3NhZmUgd2lsbCByZXN0YXJ0IGl0CiAgICAgIGlmIHRlc3QgLW4gIiRjcmFzaF9wcm90ZWN0aW9uIjsgdGhlbgogICAgICAgIGNyYXNoX3Byb3RlY3Rpb249IiIKICAgICAgICBzbGVlcCA1CiAgICAgICAgY29udGludWUgICMgQ2hlY2sgYWdhaW4uCiAgICAgIGZpCgogICAgICAjIENhbm5vdCBoZWxwIGl0CiAgICAgIGxvZ19mYWlsdXJlX21zZyAiVGhlIHNlcnZlciBxdWl0IHdpdGhvdXQgdXBkYXRpbmcgUElEIGZpbGUgKCRwaWRfZmlsZV9wYXRoKS4iCiAgICAgIHJldHVybiAxICAjIG5vdCB3YWl0aW5nIGFueSBtb3JlLgogICAgZmkKCiAgICBlY2hvICRlY2hvX24gIi4kZWNob19jIgogICAgaT1gZXhwciAkaSArIDFgCiAgICBzbGVlcCAxCgogIGRvbmUKCiAgbG9nX2ZhaWx1cmVfbXNnCiAgcmV0dXJuIDEKfQoKd2FpdF9mb3JfcmVhZHkgKCkgewoKICBpPTAKICB3aGlsZSB0ZXN0ICRpIC1uZSAkc2VydmljZV9zdGFydHVwX3RpbWVvdXQgOyBkbwoKICAgIGlmICRiaW5kaXIvbXlzcWxhZG1pbiBwaW5nID4vZGV2L251bGwgMj4mMTsgdGhlbgogICAgICBlY2hvICJvayIKCSAgI2xvZ19zdWNjZXNzX21zZwogICAgICByZXR1cm4gMAogICAgZWxpZiBraWxsIC0wICQhIDsgdGhlbgogICAgICA6ICAjIG15c3FsZF9zYWZlIGlzIHN0aWxsIHJ1bm5pbmcKICAgIGVsc2UKICAgICAgIyBteXNxbGRfc2FmZSBpcyBubyBsb25nZXIgcnVubmluZywgYWJvcnQgdGhlIHdhaXQgbG9vcAogICAgICBicmVhawogICAgZmkKCiAgICBlY2hvICRlY2hvX24gIi4kZWNob19jIgogICAgaT1gZXhwciAkaSArIDFgCiAgICBzbGVlcCAxCgogIGRvbmUKCiAgbG9nX2ZhaWx1cmVfbXNnCiAgcmV0dXJuIDEKfQojCiMgU2V0IHBpZCBmaWxlIGlmIG5vdCBnaXZlbgojCmlmIHRlc3QgLXogIiRtYXJpYWRiZF9waWRfZmlsZV9wYXRoIgp0aGVuCiAgbWFyaWFkYmRfcGlkX2ZpbGVfcGF0aD0kZGF0YWRpci9gQEhPU1ROQU1FQGAucGlkCmVsc2UKICBjYXNlICIkbWFyaWFkYmRfcGlkX2ZpbGVfcGF0aCIgaW4KICAgIC8qICkgOzsKICAgICogKSAgbWFyaWFkYmRfcGlkX2ZpbGVfcGF0aD0iJGRhdGFkaXIvJG1hcmlhZGJkX3BpZF9maWxlX3BhdGgiIDs7CiAgZXNhYwpmaQoKIyBzb3VyY2Ugb3RoZXIgY29uZmlnIGZpbGVzClsgLWYgL2V0Yy9kZWZhdWx0L215c3FsIF0gJiYgLiAvZXRjL2RlZmF1bHQvbXlzcWwKWyAtZiAvZXRjL3N5c2NvbmZpZy9teXNxbCBdICYmIC4gL2V0Yy9zeXNjb25maWcvbXlzcWwKWyAtZiAvZXRjL2NvbmYuZC9teXNxbCBdICYmIC4gL2V0Yy9jb25mLmQvbXlzcWwKCmNhc2UgIiRtb2RlIiBpbgogICdzdGFydCcpCiAgICAjIFN0YXJ0IGRhZW1vbgoKICAgICMgU2FmZWd1YXJkIChyZWxhdGl2ZSBwYXRocywgY29yZSBkdW1wcy4uKQogICAgY2QgJGJhc2VkaXIKCiAgICBlY2hvICRlY2hvX24gIlN0YXJ0aW5nIE1hcmlhREIiCiAgICBpZiB0ZXN0IC14ICRiaW5kaXIvbXlzcWxkX3NhZmUKICAgIHRoZW4KICAgICAgIyBHaXZlIGV4dHJhIGFyZ3VtZW50cyB0byBteXNxbGQgd2l0aCB0aGUgbXkuY25mIGZpbGUuIFRoaXMgc2NyaXB0CiAgICAgICMgbWF5IGJlIG92ZXJ3cml0dGVuIGF0IG5leHQgdXBncmFkZS4KICAgICAgJGJpbmRpci9teXNxbGRfc2FmZSAtLWRhdGFkaXI9IiRkYXRhZGlyIiAtLXBpZC1maWxlPSIkbWFyaWFkYmRfcGlkX2ZpbGVfcGF0aCIgIiRAIiAmCiAgICAgIHdhaXRfZm9yX3JlYWR5OyByZXR1cm5fdmFsdWU9JD8KCiAgICAgICMgTWFrZSBsb2NrIGZvciBSZWQgSGF0IC8gU3VTRQogICAgICBpZiB0ZXN0IC13ICIkbG9ja2RpciIKICAgICAgdGhlbgogICAgICAgIHRvdWNoICIkbG9ja19maWxlX3BhdGgiCiAgICAgIGZpCgogICAgICBleGl0ICRyZXR1cm5fdmFsdWUKICAgIGVsc2UKICAgICAgbG9nX2ZhaWx1cmVfbXNnICJDb3VsZG4ndCBmaW5kIE1hcmlhREIgc2VydmVyICgkYmluZGlyL215c3FsZF9zYWZlKSIKICAgIGZpCiAgICA7OwoKICAnc3RvcCcpCiAgICAjIFN0b3AgZGFlbW9uLiBXZSB1c2UgYSBzaWduYWwgaGVyZSB0byBhdm9pZCBoYXZpbmcgdG8ga25vdyB0aGUKICAgICMgcm9vdCBwYXNzd29yZC4KCiAgICBpZiB0ZXN0IC1zICIkbWFyaWFkYmRfcGlkX2ZpbGVfcGF0aCIKICAgIHRoZW4KICAgICAgbWFyaWFkYmRfcGlkPWBjYXQgIiRtYXJpYWRiZF9waWRfZmlsZV9wYXRoImAKCiAgICAgIGlmIHN1X2tpbGwgLTAgJG1hcmlhZGJkX3BpZCA7IHRoZW4KICAgICAgICBlY2hvICRlY2hvX24gIlNodXR0aW5nIGRvd24gTWFyaWFEQiIKICAgICAgICBzdV9raWxsICRtYXJpYWRiZF9waWQKICAgICAgICAjIG15c3FsZCBzaG91bGQgcmVtb3ZlIHRoZSBwaWQgZmlsZSB3aGVuIGl0IGV4aXRzLCBzbyB3YWl0IGZvciBpdC4KICAgICAgICB3YWl0X2Zvcl9nb25lICRtYXJpYWRiZF9waWQgIiRtYXJpYWRiZF9waWRfZmlsZV9wYXRoIjsgcmV0dXJuX3ZhbHVlPSQ/CiAgICAgIGVsc2UKICAgICAgICBsb2dfZmFpbHVyZV9tc2cgIk1hcmlhREIgc2VydmVyIHByb2Nlc3MgIyRtYXJpYWRiZF9waWQgaXMgbm90IHJ1bm5pbmchIgogICAgICAgIHJtICIkbWFyaWFkYmRfcGlkX2ZpbGVfcGF0aCIKICAgICAgZmkKCiAgICAgICMgRGVsZXRlIGxvY2sgZm9yIFJlZCBIYXQgLyBTdVNFCiAgICAgIGlmIHRlc3QgLWYgIiRsb2NrX2ZpbGVfcGF0aCIKICAgICAgdGhlbgogICAgICAgIHJtIC1mICIkbG9ja19maWxlX3BhdGgiCiAgICAgIGZpCiAgICAgIGV4aXQgJHJldHVybl92YWx1ZQogICAgZWxzZQogICAgICBsb2dfZmFpbHVyZV9tc2cgIk1hcmlhREIgc2VydmVyIFBJRCBmaWxlIGNvdWxkIG5vdCBiZSBmb3VuZCEiCiAgICBmaQogICAgOzsKCiAgJ3Jlc3RhcnQnKQogICAgIyBTdG9wIHRoZSBzZXJ2aWNlIGFuZCByZWdhcmRsZXNzIG9mIHdoZXRoZXIgaXQgd2FzCiAgICAjIHJ1bm5pbmcgb3Igbm90LCBzdGFydCBpdCBhZ2Fpbi4KICAgIGlmICQwIHN0b3AgICIkQCI7IHRoZW4KICAgICAgaWYgISAkMCBzdGFydCAiJEAiOyB0aGVuCiAgICAgICAgbG9nX2ZhaWx1cmVfbXNnICJGYWlsZWQgdG8gcmVzdGFydCBzZXJ2ZXIuIgogICAgICAgIGV4aXQgMQogICAgICBmaQogICAgZWxzZQogICAgICBsb2dfZmFpbHVyZV9tc2cgIkZhaWxlZCB0byBzdG9wIHJ1bm5pbmcgc2VydmVyLCBzbyByZWZ1c2luZyB0byB0cnkgdG8gc3RhcnQuIgogICAgICBleGl0IDEKICAgIGZpCiAgICA7OwoKICAncmVsb2FkJ3wnZm9yY2UtcmVsb2FkJykKICAgIGlmIHRlc3QgLXMgIiRtYXJpYWRiZF9waWRfZmlsZV9wYXRoIiA7IHRoZW4KICAgICAgcmVhZCBtYXJpYWRiZF9waWQgPCAgIiRtYXJpYWRiZF9waWRfZmlsZV9wYXRoIgogICAgICBzdV9raWxsIC1IVVAgJG1hcmlhZGJkX3BpZAogICAgICB0b3VjaCAiJG1hcmlhZGJkX3BpZF9maWxlX3BhdGgiCiAgICBlbHNlCiAgICAgIGxvZ19mYWlsdXJlX21zZyAiTWFyaWFEQiBQSUQgZmlsZSBjb3VsZCBub3QgYmUgZm91bmQhIgogICAgICBleGl0IDEKICAgIGZpCiAgICA7OwogICdzdGF0dXMnKQogICAgIyBGaXJzdCwgY2hlY2sgdG8gc2VlIGlmIHBpZCBmaWxlIGV4aXN0cwogICAgaWYgdGVzdCAtcyAiJG1hcmlhZGJkX3BpZF9maWxlX3BhdGgiIDsgdGhlbgogICAgICByZWFkIG1hcmlhZGJkX3BpZCA8ICIkbWFyaWFkYmRfcGlkX2ZpbGVfcGF0aCIKICAgICAgaWYgc3Vfa2lsbCAtMCAkbWFyaWFkYmRfcGlkIDsgdGhlbgogICAgICAgIGVjaG8gIm9rIgoJCSNsb2dfc3VjY2Vzc19tc2cgIk1hcmlhREIgcnVubmluZyAoJG1hcmlhZGJkX3BpZCkiCiAgICAgICAgZXhpdCAwCiAgICAgIGVsc2UKICAgICAgICBsb2dfZmFpbHVyZV9tc2cgIk1hcmlhREIgaXMgbm90IHJ1bm5pbmcsIGJ1dCBQSUQgZmlsZSBleGlzdHMiCiAgICAgICAgZXhpdCAxCiAgICAgIGZpCiAgICBlbHNlCiAgICAgICMgVHJ5IHRvIGZpbmQgYXBwcm9wcmlhdGUgbWFyaWFkYmQgcHJvY2VzcwogICAgICBtYXJpYWRiZF9waWQ9YHBncmVwIC1mICRsaWJleGVjZGlyL21hcmlhZGJkYAoKICAgICAgIyB0ZXN0IGlmIG11bHRpcGxlIHBpZHMgZXhpc3QKICAgICAgcGlkX2NvdW50PWBlY2hvICRtYXJpYWRiZF9waWQgfCB3YyAtd2AKICAgICAgaWYgdGVzdCAkcGlkX2NvdW50IC1ndCAxIDsgdGhlbgogICAgICAgIGxvZ19mYWlsdXJlX21zZyAiTXVsdGlwbGUgTWFyaWFEQiBydW5uaW5nIGJ1dCBQSUQgZmlsZSBjb3VsZCBub3QgYmUgZm91bmQgKCRtYXJpYWRiZF9waWQpIgogICAgICAgIGV4aXQgNQogICAgICBlbGlmIHRlc3QgLXogJG1hcmlhZGJkX3BpZCA7IHRoZW4KICAgICAgICBpZiB0ZXN0IC1mICIkbG9ja19maWxlX3BhdGgiIDsgdGhlbgogICAgICAgICAgbG9nX2ZhaWx1cmVfbXNnICJNYXJpYURCIGlzIG5vdCBydW5uaW5nLCBidXQgbG9jayBmaWxlICgkbG9ja19maWxlX3BhdGgpIGV4aXN0cyIKICAgICAgICAgIGV4aXQgMgogICAgICAgIGZpCiAgICAgICAgbG9nX2ZhaWx1cmVfbXNnICJNYXJpYURCIGlzIG5vdCBydW5uaW5nIgogICAgICAgIGV4aXQgMwogICAgICBlbHNlCiAgICAgICAgbG9nX2ZhaWx1cmVfbXNnICJNYXJpYURCIGlzIHJ1bm5pbmcgYnV0IFBJRCBmaWxlIGNvdWxkIG5vdCBiZSBmb3VuZCIKICAgICAgICBleGl0IDQKICAgICAgZmkKICAgIGZpCiAgICA7OwogICdjb25maWd0ZXN0JykKICAgICMgU2FmZWd1YXJkIChyZWxhdGl2ZSBwYXRocywgY29yZSBkdW1wcy4uKQogICAgY2QgJGJhc2VkaXIKICAgIGVjaG8gJGVjaG9fbiAiVGVzdGluZyBNYXJpYURCIGNvbmZpZ3VyYXRpb24gc3ludGF4IgogICAgZGFlbW9uPSRiaW5kaXIvbWFyaWFkYmQKICAgIGlmIHRlc3QgLXggJGxpYmV4ZWNkaXIvbWFyaWFkYmQKICAgIHRoZW4KICAgICAgZGFlbW9uPSRsaWJleGVjZGlyL21hcmlhZGJkCiAgICBlbGlmIHRlc3QgLXggJHNiaW5kaXIvbWFyaWFkYmQKICAgIHRoZW4KICAgICAgZGFlbW9uPSRzYmluZGlyL21hcmlhZGJkCiAgICBlbGlmIHRlc3QgLXggYHdoaWNoIG1hcmlhZGJkYAogICAgdGhlbgogICAgICBkYWVtb249YHdoaWNoIG1hcmlhZGJkYAogICAgZWxzZQogICAgICBsb2dfZmFpbHVyZV9tc2cgIlVuYWJsZSB0byBsb2NhdGUgdGhlIG1hcmlhZGJkIGJpbmFyeSEiCiAgICAgIGV4aXQgMQogICAgZmkKICAgIGhlbHBfb3V0PWAkZGFlbW9uIC0taGVscCAyPiYxYDsgcj0kPwogICAgaWYgdGVzdCAiJHIiICE9IDAgOyB0aGVuCiAgICAgIGxvZ19mYWlsdXJlX21zZyAiJGhlbHBfb3V0IgogICAgICBsb2dfZmFpbHVyZV9tc2cgIlRoZXJlIGFyZSBzeW50YXggZXJyb3JzIGluIHRoZSBzZXJ2ZXIgY29uZmlndXJhdGlvbi4gUGxlYXNlIGZpeCB0aGVtISIKICAgIGVsc2UKICAgICAgZWNobyAib2siCgkgICNsb2dfc3VjY2Vzc19tc2cgIlN5bnRheCBPSyIKICAgIGZpCiAgICBleGl0ICRyCiAgICA7OwogICdib290c3RyYXAnKQogICAgICBpZiB0ZXN0ICIkX3VzZV9zeXN0ZW1jdGwiID09IDEgOyB0aGVuCiAgICAgICAgbG9nX2ZhaWx1cmVfbXNnICJQbGVhc2UgdXNlIGdhbGVyYV9uZXdfY2x1c3RlciB0byBzdGFydCB0aGUgbWFyaWFkYiBzZXJ2aWNlIHdpdGggLS13c3JlcC1uZXctY2x1c3RlciIKICAgICAgICBleGl0IDEKICAgICAgZmkKICAgICAgIyBCb290c3RyYXAgdGhlIGNsdXN0ZXIsIHN0YXJ0IHRoZSBmaXJzdCBub2RlCiAgICAgICMgdGhhdCBpbml0aWF0ZSB0aGUgY2x1c3RlcgogICAgICBlY2hvICRlY2hvX24gIkJvb3RzdHJhcHBpbmcgdGhlIGNsdXN0ZXIuLiAiCiAgICAgICQwIHN0YXJ0ICRvdGhlcl9hcmdzIC0td3NyZXAtbmV3LWNsdXN0ZXIKICAgICAgZXhpdCAkPwogICAgICA7OwogICopCiAgICAgICMgdXNhZ2UKICAgICAgYmFzZW5hbWU9YGJhc2VuYW1lICIkMCJgCiAgICAgIGVjaG8gIlVzYWdlOiAkYmFzZW5hbWUgIHtzdGFydHxzdG9wfHJlc3RhcnR8cmVsb2FkfGZvcmNlLXJlbG9hZHxzdGF0dXN8Y29uZmlndGVzdHxib290c3RyYXB9ICBbIE1hcmlhREIgc2VydmVyIG9wdGlvbnMgXSIKICAgICAgZXhpdCAxCiAgICA7Owplc2FjCgpleGl0IDAK"
rMySQLINIT = base64.b64decode(eMySQLINITenc).decode('utf-8')

rConfigPath = "/home/xtreamcodes/iptv_xtream_codes/config"

class col:
    HEADER = '\033[95m'
    OKBLUE = '\033[94m'
    OKGREEN = '\033[92m'
    WARNING = '\033[93m'
    FAIL = '\033[91m'
    ENDC = '\033[0m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'


def generate(length=16):
    return ''.join(random.choice(string.ascii_letters + string.digits) for i in range(length))


def getIP():
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    s.connect(("8.8.8.8", 80))
    return s.getsockname()[0]


def getVersion():
    try:
        return subprocess.check_output("lsb_release -d".split()).split(":")[-1].strip()
    except:
        return ""


def printc(rText, rColour=col.OKBLUE, rPadding=0):
    print("%s ┌────── ODIN FREE CLEAN INSTALL - DISCORD: https://discord.gg/mH6D7VWXmt ─────┐ %s" % (
        rColour, col.ENDC))
    for i in range(rPadding): print("%s                                                              %s" % (
        rColour, col.ENDC))
    print("%s           %s%s%s            %s" % (
        rColour, " " * (20 - (len(rText) // 2)), rText, " " * (40 - (20 - (len(rText) // 2)) - len(rText)),
        col.ENDC))
    for i in range(rPadding): print("%s                                                               %s" % (
        rColour, col.ENDC))
    print("%s └─────────────────────────────────────────────────────────────────────────────┘ %s" % (
        rColour, col.ENDC))
    print(" ")


def prepare(rType="MAIN"):
    #global rPackages
    #if rType != "MAIN": rPackages = rPackages[:-1]
    printc("Preparing Installation")
    try:
        subprocess.check_output("getent passwd xtreamcodes > /dev/null".split())
    except:
        # Create User
        printc("Creating user")
        
    # Create User
    printc("Creating user")
    os.system("adduser --system --shell /bin/false --group --disabled-login xtreamcodes >/dev/null 2>&1")
    os.system("adduser --system --shell /bin/false xtreamcodes >/dev/null 2>&1")
    if not os.path.exists("/home/xtreamcodes"): os.mkdir("/home/xtreamcodes")
    return True


def install(rType="MAIN"):
    global rInstall, rDownloadURL
    printc("Downloading Software")
    try:
        rURL = rDownloadURL[rInstall[rType]]
    except:
        printc("Invalid download URL!", col.FAIL)
        return False
    os.system('wget -q -O "/tmp/install.sh" "%s"' % rURL)
    if os.path.exists("/tmp/install.sh"):
        printc("Installing Software")
        os.system('bash /tmp/install.sh >/dev/null 2>&1')
        try:
            os.remove("/tmp/install.sh")
        except:
            pass
        return True
    printc("Failed to download installation file!", col.FAIL)
    return False


def mysql(rUsername, rPassword):
    global rMySQLCnf
    printc("Configuring MariaDB- wait maybe slowly")
    rCreate = True
    if os.path.exists("/etc/mysql/my.cnf"):
        shutil.copy("/etc/mysql/my.cnf", "/etc/mysql/my.cnf.xc")
    if rCreate:
        os.system('mkdir -p /etc/mysql/ > /dev/null')
        rFile = open("/etc/mysql/my.cnf", "w")
        rFile.write(rMySQLCnf)
        rFile.close()
    if os.path.exists("/etc/my.cnf"):    
        shutil.copy("/etc/my.cnf", "/etc/my.cnf.xc")
    if rCreate:
        rFile = open("/etc/my.cnf", "w")
        rFile.write(rMySQLCnf)
        rFile.close()
    os.system("rm -rf /etc/systemd/system/mariadb.service.d /etc/systemd/system/multi-user.target.wants/mariadb.service >/dev/null 2>&1")
    os.system('mkdir -p /etc/init.d/ >/dev/null 2>&1')
    os.system('touch /etc/init.d/mariadb >/dev/null 2>&1')
    rFile = open("/etc/init.d/mariadb", "w")
    rFile.write(rMySQLINIT)
    rFile.close()
    os.system("chmod 777 /etc/init.d/mariadb >/dev/null 2>&1")
    os.system("systemctl restart mariadb >/dev/null 2>&1")
    os.system("service mariadb restart >/dev/null 2>&1")
    os.system("systemctl mariadb enable >/dev/null 2>&1")
    os.system("chkconfig --add mariadb >/dev/null 2>&1")
    os.system("chkconfig --level 345 mariadb on >/dev/null 2>&1")
    os.system("update-rc.d mariadb defaults >/dev/null 2>&1")
    os.system("service mariadb restart >/dev/null 2>&1")
        
    #printc("Enter MySQL Root Password:", col.WARNING)
    for i in range(5):
        rMySQLRoot = "" #raw_input("  ")
        print (" ")
                               
        if len(rMySQLRoot) > 0: rExtra = " -p%s" % rMySQLRoot
             
        else: rExtra = ""
        printc("Drop existing & create database? Y/N", col.WARNING)
        if input("  ").upper() == "Y": rDrop = True
                        
             
        else: rDrop = False
        try:
            if rDrop:
                os.system("rm -rf /etc/systemd/system/mariadb.service.d /etc/systemd/system/multi-user.target.wants/mariadb.service >/dev/null 2>&1")
                os.system('mkdir -p /etc/init.d/ >/dev/null 2>&1')
                os.system('touch /etc/init.d/mariadb >/dev/null 2>&1')
                rFile = open("/etc/init.d/mariadb", "w")
                rFile.write(rMySQLINIT)
                rFile.close()
                os.system("chmod 777 /etc/init.d/mariadb >/dev/null 2>&1")
                os.system("systemctl restart mariadb >/dev/null 2>&1")
                os.system("service mariadb restart >/dev/null 2>&1")
                os.system("systemctl mariadb enable >/dev/null 2>&1")
                os.system("chkconfig --add mariadb >/dev/null 2>&1")
                os.system("chkconfig --level 345 mariadb on >/dev/null 2>&1")
                os.system("update-rc.d mariadb defaults >/dev/null 2>&1")
                os.system("service mariadb restart >/dev/null 2>&1")
                os.system(
                    'mysql -u root%s -e "DROP DATABASE IF EXISTS xtream_iptvpro; CREATE DATABASE IF NOT EXISTS xtream_iptvpro;" 2> /dev/null' % rExtra)
                os.system(
                    "mysql -u root%s xtream_iptvpro < /home/xtreamcodes/iptv_xtream_codes/database.sql 2> /dev/null" % rExtra)

                # UPDATE V46 CREATE TABLE ODIN_BLOCKER
                os.system(
                    'mysql -u root%s -e "USE xtream_iptvpro; DROP TABLE IF EXISTS odin_blocker; CREATE TABLE odin_blocker (id int(11) NOT NULL AUTO_INCREMENT,timestamp datetime DEFAULT NULL,ip varchar(255) DEFAULT NULL,PRIMARY KEY (id));" 2> /dev/null' % rExtra)

                os.system(
                    'mysql -u root%s -e "USE xtream_iptvpro; UPDATE settings SET live_streaming_pass = \'%s\', unique_id = \'%s\', crypt_load_balancing = \'%s\';" 2> /dev/null' % (
                        rExtra, generate(20), generate(10), generate(20)))
                os.system(
                    'mysql -u root%s -e "USE xtream_iptvpro; REPLACE INTO streaming_servers (id, server_name, domain_name, server_ip, vpn_ip, ssh_password, ssh_port, diff_time_main, http_broadcast_port, total_clients, system_os, network_interface, latency, status, enable_geoip, geoip_countries, last_check_ago, can_delete, server_hardware, total_services, persistent_connections, rtmp_port, geoip_type, isp_names, isp_type, enable_isp, boost_fpm, http_ports_add, network_guaranteed_speed, https_broadcast_port, https_ports_add, whitelist_ips, watchdog_data, timeshift_only) VALUES (1, \'Main Server\', \'\', \'%s\', \'\', NULL, NULL, 0, 25461, 1000, \'%s\', \'eth0\', 0, 1, 0, \'\', 0, 0, \'{}\', 3, 0, 25462, \'low_priority\', \'\', \'low_priority\', 0, 1, \'\', 1000, 25463, \'\', \'[\"127.0.0.1\",\"\"]\', \'{}\', 0);" 2> /dev/null' % (
                        rExtra, getIP(), getVersion()))
                os.system(
                    'mysql -u root%s -e "USE xtream_iptvpro; REPLACE INTO reg_users (id, username, password, email, member_group_id, verified, status) VALUES (1, \'admin\', \'\$6\$rounds=20000\$xtreamcodes\$XThC5OwfuS0YwS4ahiifzF14vkGbGsFF1w7ETL4sRRC5sOrAWCjWvQJDromZUQoQuwbAXAFdX3h3Cp3vqulpS0\', \'admin@website.com\', 1, 1, 1);" 2> /dev/null' % rExtra)
            os.system(
                'mysql -u root%s -e "GRANT ALL PRIVILEGES ON *.* TO \'%s\'@\'%%\' IDENTIFIED BY \'%s\' WITH GRANT OPTION; FLUSH PRIVILEGES;" 2> /dev/null' % (
                    rExtra, rUsername, rPassword))
            try:
                os.remove("/home/xtreamcodes/iptv_xtream_codes/database.sql")
            except:
                pass
            return True
        except:
            printc("Invalid password! Try again", col.FAIL)
    return False


def encrypt(rHost="127.0.0.1", rUsername="user_iptvpro", rPassword="", rDatabase="xtream_iptvpro", rServerID=1,
            rPort=7999):
    printc("Encrypting...")
    # try: os.remove(rConfigPath)
    # except: pass

    with open(rConfigPath, 'wb') as rf:
        data = ''.join(chr(ord(c) ^ ord(k)) for c, k in
                       zip('{"host":"%s","db_user":"%s","db_pass":"%s","db_name":"%s","server_id":"%d", "db_port":"%d"}' % (
                           rHost, rUsername, rPassword, rDatabase, rServerID, rPort),
                           cycle('5709650b0d7806074842c6de575025b1')))
        encoded_data = base64.b64encode(data.encode()).decode().replace('\n', '')
        rf.write(encoded_data.encode())


def configure():
    printc("Configuring System")
    os.system("touch /etc/fstab")
    if not "/home/xtreamcodes/iptv_xtream_codes/" in open("/etc/fstab").read():
        rFile = open("/etc/fstab", "a")
        rFile.write(
            "tmpfs /home/xtreamcodes/iptv_xtream_codes/streams tmpfs defaults,noatime,nosuid,nodev,noexec,mode=1777,size=90% 0 0\ntmpfs /home/xtreamcodes/iptv_xtream_codes/tmp tmpfs defaults,noatime,nosuid,nodev,noexec,mode=1777,size=2G 0 0")
        rFile.close()
    if not "xtreamcodes" in open("/etc/sudoers").read():
        os.system('echo "xtreamcodes ALL=(root) NOPASSWD: /sbin/iptables, /usr/bin/chattr" >> /etc/sudoers')
    if not os.path.exists("/etc/init.d/xtreamcodes"):
        rStart = open("/etc/init.d/xtreamcodes", "w")
        rStart.write(
            "#!/bin/bash\n### BEGIN INIT INFO\n# Provides:          xtreamcodes\n# Required-Start:    $all\n# Required-Stop:\n# Default-Start:     2 3 4 5\n# Default-Stop:\n# Short-Description: Run /etc/init.d/xtreamcodes if it exist\n### END INIT INFO\nsleep 1\n/home/xtreamcodes/iptv_xtream_codes/start_services.sh > /dev/null")
        rStart.close()
        os.system("chmod +x /etc/init.d/xtreamcodes")
        os.system("update-rc.d xtreamcodes defaults >/dev/null 2>&1")
        os.system("update-rc.d xtreamcodes enable >/dev/null 2>&1")
    try:
        os.remove("/usr/bin/ffmpeg")
    except:
        pass
    if not os.path.exists("/home/xtreamcodes/iptv_xtream_codes/tv_archive"): os.mkdir(
        "/home/xtreamcodes/iptv_xtream_codes/tv_archive/")
    os.system("ln -s /home/xtreamcodes/iptv_xtream_codes/bin/ffmpeg /usr/bin/")
    os.system("chown xtreamcodes:xtreamcodes -R /home/xtreamcodes > /dev/null")
    os.system("chmod -R 0777 /home/xtreamcodes > /dev/null")
    os.system("chmod +x /home/xtreamcodes/iptv_xtream_codes/start_services.sh > /dev/null")
    os.system("chattr -i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb > /dev/null")
    os.system("mount -a >/dev/null 2>&1")
    os.system("chattr -i /etc/hosts >/dev/null 2>&1")
    os.system("chmod 777 /etc/hosts >/dev/null 2>&1")
    if not "api.xtream-codes.com" in open("/etc/hosts").read(): os.system(
        'echo "127.0.0.1    api.xtream-codes.com" >> /etc/hosts')
    if not "downloads.xtream-codes.com" in open("/etc/hosts").read(): os.system(
        'echo "127.0.0.1    downloads.xtream-codes.com" >> /etc/hosts')
    if not " xtream-codes.com" in open("/etc/hosts").read(): os.system(
        'echo "127.0.0.1    xtream-codes.com" >> /etc/hosts')
    os.system("chattr +i /etc/hosts >/dev/null 2>&1")
    os.system(
        "sed -i 's|echo \"ODIN IpTV Panel https://discord.gg/mH6D7VWXmt \";|header(\"Location: https://www.google.com/\");|g' /home/xtreamcodes/iptv_xtream_codes/wwwdir/index.php")
    printc("INSTALLING AND UPDATING YOUTUBE MODULE")
    os.system("sudo wget https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -O /usr/local/bin/youtube-dl 2> /dev/null")
    os.system("sudo chmod a+rx /usr/local/bin/youtube-dl > /dev/null")
    os.system("cp /usr/local/bin/youtube-dl /home/xtreamcodes/iptv_xtream_codes/bin/ > /dev/null")
    os.system("sudo chmod a+rx /home/xtreamcodes/iptv_xtream_codes/bin/youtube-dl > /dev/null")


def start():
    os.system("chattr +i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb 2>/dev/null")
    os.system("chmod 644 /home/xtreamcodes/iptv_xtream_codes/php/VaiIb8.pid 2>/dev/null")
    os.system("chmod 644 /home/xtreamcodes/iptv_xtream_codes/php/JdlJXm.pid 2>/dev/null")
    os.system("chmod 644 /home/xtreamcodes/iptv_xtream_codes/php/CWcfSP.pid 2>/dev/null")
    os.system("chmod +x /home/xtreamcodes/iptv_xtream_codes/nginx/sbin/nginx 2>/dev/null")
    os.system("chmod +x /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx_rtmp 2>/dev/null")
    os.system("chmod +x /home/xtreamcodes/iptv_xtream_codes/php/bin/php 2>/dev/null")
    os.system("chmod +x /home/xtreamcodes/iptv_xtream_codes/php/sbin/php-fpm 2>/dev/null")
    os.system("sysctl -w kernel.core_pattern='|/bin/false' >/dev/null 2>&1")
    os.system("wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/start_services.sh -qO /home/xtreamcodes/iptv_xtream_codes/start_services.sh")
    os.system("chmod 777 /home/xtreamcodes/iptv_xtream_codes/start_services.sh")
    os.system("wget -qO /tmp/install-bin-packages.sh https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/install-bin-packages.sh >/dev/null 2>&1")
    os.system('bash /tmp/install-bin-packages.sh >/dev/null 2>&1')
    os.system('rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/ >/dev/null 2>&1')
    os.system("/home/xtreamcodes/iptv_xtream_codes/start_services.sh >/dev/null 2>&1")


def modifyNginx():
    printc("Modifying Nginx")
    rPath = "/home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf"
    rPrevData = open(rPath, "r").read()
    if not "listen 25500;" in rPrevData:
        shutil.copy(rPath, "%s.xc" % rPath)
        rData = "}".join(rPrevData.split("}")[
                         :-1]) + "\n    server {\n        listen 25500;\n        index index.php index.html index.htm;\n        root /home/xtreamcodes/iptv_xtream_codes/admin/;\n\n        location ~ \.php$ {\n			limit_req zone=one burst=8;\n            try_files $uri =404;\n			fastcgi_index index.php;\n			fastcgi_pass php;\n			include fastcgi_params;\n			fastcgi_buffering on;\n			fastcgi_buffers 96 32k;\n			fastcgi_buffer_size 32k;\n			fastcgi_max_temp_file_size 0;\n			fastcgi_keep_conn on;\n			fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n			fastcgi_param SCRIPT_NAME $fastcgi_script_name;\n        }\n    }\n#ISP CONFIGURATION\n\n    server {\n        listen 8805;\n        root /home/xtreamcodes/iptv_xtream_codes/isp/;\n        location / {\n            allow 127.0.0.1;\n            deny all;\n        }\n        location ~ \.php$ {\n			limit_req zone=one burst=8;\n            try_files $uri =404;\n			fastcgi_index index.php;\n			fastcgi_pass php;\n			include fastcgi_params;\n			fastcgi_buffering on;\n			fastcgi_buffers 96 32k;\n			fastcgi_buffer_size 32k;\n			fastcgi_max_temp_file_size 0;\n			fastcgi_keep_conn on;\n			fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n			fastcgi_param SCRIPT_NAME $fastcgi_script_name;\n        }\n    }\n}"
        rFile = open(rPath, "w")
        rFile.write(rData)
        rFile.close()


if __name__ == "__main__":
    #os.system('clear')
    #installsscreen()
    printc("ODIN - CLEAN MARIADB INSTALLATION TO Ubuntu 18.04", col.OKGREEN, 2)
    try:
        rType = input("  INSTALLATION TYPE [MAIN, LB]: ")
    except KeyboardInterrupt:
        print("\nExiting...")
        sys.exit(0)
    
    #rType = input("  INSTALLATION TYPE [MAIN, LB]: ")
    print(" ")
    #rType = "MAIN"
    if rType.upper() in ["MAIN", "LB"]:
    #rType.upper() = MAIN
        if rType.upper() == "LB":
            rHost = input("  Main Server IP Address: ")
            rPassword = input("  MySQL Password: ")
            try:
                rServerID = int(input("  Load Balancer Server ID: "))
            except:
                rServerID = -1
            print(" ")
        else:
            rHost = "127.0.0.1"
            rPassword = generate()
            rServerID = 1
        rUsername = "user_iptvpro"
        rDatabase = "xtream_iptvpro"
        rPort = 7999
        if len(rHost) > 0 and len(rPassword) > 0 and rServerID > -1:
            printc("START INSTALLATION? Y/N", col.WARNING)
            if input("  ").upper() == "Y":
                print(" ")
                rRet = prepare(rType.upper())
                if not install(rType.upper()): sys.exit(1)
                if rType.upper() == "MAIN":
                    if not mysql(rUsername, rPassword): sys.exit(1)
                encrypt(rHost, rUsername, rPassword, rDatabase, rServerID, rPort)
                configure()
                if rType.upper() == "MAIN": modifyNginx()
                start()
                printc("ODIN INSTALLATION COMPLETED!", col.OKGREEN, 2)
                printc("YOUR ODIN ADMIN DASHBOARD IS: http://%s:25500" % getIP())
                if rType.upper() == "MAIN":
                    printc("Please store your MySQL password!")
                    printc(rPassword)
            else:
                printc("Installation cancelled", col.FAIL)
        else:
            printc("Invalid entries", col.FAIL)
    else:
        printc("Invalid installation type", col.FAIL)

