#!/bin/bash

# AWK -> RS: separador de registro
# CUT -> -d delimitador -f el campo
# TR -> -d borrar

RESULT=`curl "https://sandbox.musement.com/api/v3/cities?offset=0&sort_by=weight&without_events=no"| awk 'BEGIN{RS=","}{print $0}' | fgrep '"code"' | cut -d: -f2 | tr -d '\n' |  awk 'BEGIN{RS="\"\""}{print $0}' | tr -d '"'`

for city in $RESULT
do

  OUTPUT="$city"

  RESULT_FORECAST=`curl "http://api.weatherapi.com/v1/forecast.xml?key=bf9105ce705f479ba9c145406210311&q=amsterdam&days=1&aqi=no&alerts=no" | awk 'BEGIN{RS=">"}{print $0}' | fgrep text | fgrep -v '<text' | cut -d'<' -f1 | head -2`

  OLDIFS=$IFS
  IFS=$'\n'
  for forecast in $RESULT_FORECAST
  do
    OUTPUT="$OUTPUT - $forecast"
  done
  IFS=$OLDIFS

  echo $OUTPUT

  break
done

