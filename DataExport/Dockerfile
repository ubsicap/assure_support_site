FROM python:3.11-slim

WORKDIR /app
COPY DataExport.py .

RUN pip install mysql-connector-python
RUN pip install secure-smtplib

CMD ["python", "./DataExport.py", "20", "1"]
