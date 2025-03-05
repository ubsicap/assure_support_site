#!/bin/sh -x
#
#  $1 - user/image:version
#
#  get db keys from kubernetes and format into
#  KEY=VALUE pairs
#
echo DB_KEY= > C:\\temp\\temp1.txt
kubectl get secret db-user -o jsonpath='{.data.db-key}' -n support-bible | base64 --decode > C:\\temp\\temp2.txt
x=$(cat C:\\temp\\temp1.txt C:\\temp\\temp2.txt)
echo -n $x > C:\\temp\\temp3.txt
sed 's/= /=/' < C:\\temp\\temp3.txt > C:\\temp\\db_key.txt


echo DB_HOST= > C:\\temp\\temp1.txt
kubectl get secret db-user -o jsonpath='{.data.db-host}' -n support-bible | base64 --decode > C:\\temp\\temp2.txt
x=$(cat C:\\temp\\temp1.txt C:\\temp\\temp2.txt)
echo -n $x > C:\\temp\\temp3.txt
sed 's/= /=/' < C:\\temp\\temp3.txt > C:\\temp\\db_host.txt


echo TOKEN_KEY= > C:\\temp\\temp1.txt
kubectl get secret db-user -o jsonpath='{.data.token-key}' -n support-bible | base64 --decode > C:\\temp\\temp2.txt
x=$(cat C:\\temp\\temp1.txt C:\\temp\\temp2.txt)
echo -n $x > C:\\temp\\temp3.txt
sed 's/= /=/' < C:\\temp\\temp3.txt > C:\\temp\\token-key.txt


echo IV_KEY= > C:\\temp\\temp1.txt
kubectl get secret db-user -o jsonpath='{.data.iv-key}' -n support-bible | base64 --decode > C:\\temp\\temp2.txt
x=$(cat C:\\temp\\temp1.txt C:\\temp\\temp2.txt)
echo -n $x > C:\\temp\\temp3.txt
sed 's/= /=/' < C:\\temp\\temp3.txt > C:\\temp\\iv-key.txt

echo "" > C:\\temp\\newline.txt
cat C:\\temp\\db_key.txt C:\\temp\\newline.txt C:\\temp\\db_host.txt C:\\temp\\newline.txt C:\\temp\\token-key.txt C:\\temp\\newline.txt C:\\temp\\iv-key.txt > C:\\temp\\env.txt


cmd="docker build"
while read -r p || [ -n "$p" ]
do
  cmd+=" --build-arg "$p""
done <C:\\temp\\env.txt
cmd+=" -t $1 ."
echo cmd : $cmd

eval "$cmd"

docker push $1

