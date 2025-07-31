FROM python:3.11-slim

WORKDIR /app
COPY DataExport.py .

RUN pip install mysql-connector-python
RUN apt update
RUN apt install tar

CMD ["python", "./DataExport.py", "20", "1"]