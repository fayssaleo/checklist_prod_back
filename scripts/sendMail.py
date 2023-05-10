import smtplib,ssl
from email import encoders
from email.mime.base import MIMEBase
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from datetime import datetime
import json
import sys

def replaceInHTML(damage):
    return """\
    <html lang="en">
      <head>
        <style>
          .title1 {
            width: 80%;
            margin: 0 auto;
            margin-bottom: 20px;
          }
          table {
            font-size: 20px;
            border: 2px solid black;
            border-collapse: collapse;
            width: 80%;
            margin: 0 auto;
          }
          tr {
            border: 2px solid black;
          }
          th {
            background-color: #d4dd56;
            padding: 0;
            width: 200px;
            margin: 0;
          }
          th,
          td {
            text-align: center;
            margin: 1px 20px;
            border: 2px solid black;
          }
          td:first-child {
            color: black;
            background-color: #ccc;
            padding: 5px;
            min-width: 115px;
          }
          td:nth-child(2) {
            color: black;
            background-color: #cccccc42 !important;
            padding: 5px;
          }
          .separator {
            background-color: #cccccc17 !important;
          }
          h5 {
            margin: 0;
            padding: 0;
          }
        </style>
      </head>
      <body>
        <h2 class="title1">Damage Reports</h2>
        <table>
          <tr>
            <td><h5>Damage Type</h5></td>
            <td>"""+damage["id"]+"""</td>
          </tr>
          <tr>
            <td><h5>Description</h5></td>
            <td>phone and tablet5 phone and tablet5</td>
          </tr>
          <tr>
            <td><h5>Status</h5></td>
            <td>On progress</td>
          </tr>
          <tr>
            <td><h5>Shift</h5></td>
            <td>phone and tablet5</td>
          </tr>
          <tr rowspan="5">
            <td colspan="2" class="separator"></td>
          </tr>
          <tr>
            <td><h5>Driver In</h5></td>
            <td>phone and tablet5</td>
          </tr>
          <tr>
            <td><h5>Driver Out</h5></td>
            <td>phone and tablet5</td>
          </tr>
          <tr>
            <td><h5>Declared At</h5></td>
            <td>phone and tablet5</td>
          </tr>
          <tr rowspan="5">
            <td colspan="2" class="separator"></td>
          </tr>
          <tr>
            <td><h5>resolved By</h5></td>
            <td>phone and tablet5</td>
          </tr>
          <tr>
            <td><h5>resolved Description</h5></td>
            <td>phone and tablet5</td>
          </tr>
          <tr>
            <td><h5>resolved At</h5></td>
            <td>phone and tablet5</td>
          </tr>
          <tr rowspan="5">
            <td colspan="2" class="separator"></td>
          </tr>
          <tr>
            <td><h5>Closed By</h5></td>
            <td>phone and tablet5</td>
          </tr>
          <tr>
            <td><h5>Closed At</h5></td>
            <td>phone and tablet5</td>
          </tr>
          <tr rowspan="5">
            <td colspan="2" class="separator"></td>
          </tr>
          <tr>
            <td><h5>rejected By</h5></td>
            <td>phone and tablet5</td>
          </tr>
          <tr>
            <td><h5>rejected Reason</h5></td>
            <td>phone and tablet5</td>
          </tr>
          <tr>
            <td><h5>rejected At</h5></td>
            <td>phone and tablet5</td>
          </tr>
          <tr>
            <td><h5>rejected Times</h5></td>
            <td>phone and tablet5</td>
          </tr>
        </table>
      </body>
    </html>

    """

currentDate = str(datetime.now().date())+'T'+str(datetime.now().time())[:8]
print(str(sys.argv[3]))
damage=json.loads(str(sys.argv[3]))

if not sys.argv[1]:
    html = """<h2>There was an exceptional error, please look into the issue(it might be a server issue, or a connexion problem).</h2>"""
else:
    html = replaceInHTML(damage)
context = ssl.create_default_context()
message = MIMEMultipart("alternative")
message["Subject"] = "ETC dailyreport "+currentDate
message["From"] = 'no-reply@tangeralliance.com'
message["To"] = 'fayssal.ourezzouq@tangeralliance.com'

message.attach(MIMEText(html, "html"))



with open(str("C:/Users/fayssal.ourezzouq/Desktop/apps/CHECKLIST/checklist_backend/scripts/ff.png"), "rb") as attachment:
# Add file as application/octet-stream
# Email client can usually download this automatically as attachment
    part = MIMEBase("application", "octet-stream")
    part.set_payload(attachment.read())

# Encode file in ASCII characters to send by email
encoders.encode_base64(part)

# Add header as key/value pair to attachment part
part.add_header(
    "Content-Disposition",
    f"attachment; filename="+str("./ff.png"),
)

# Add attachment to message and convert message to string
message.attach(part)

with smtplib.SMTP("smtp.office365.com", 587,timeout=120) as server:
    server.ehlo()  # Can be omitted
    server.starttls(context=context)
    server.ehlo()  # Can be omitted
    server.login('no-reply@tangeralliance.com', "TA@nn111gier$2021@")
    server.sendmail('no-reply@tangeralliance.com', str(sys.argv[2]), message.as_string())
print("dddd")
sys.stdout.flush()
