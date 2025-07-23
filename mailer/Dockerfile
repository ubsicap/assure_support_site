FROM python:3.11-slim

WORKDIR /app
COPY mailer.py .

RUN pip install mysql-connector-python
RUN pip install secure-smtplib

CMD ["python", "./mailer.py", "3"]
