import csv
import requests
from unidecode import unidecode
from bs4 import BeautifulSoup
import html5lib




def get_link(url,category):
    response=requests.get(url)
    data=response.content
    soup=BeautifulSoup(data,'html5lib')
    ul=soup.find('ul',attrs={'id' : 'catalog_listings'})
    li=ul.findAll('li', attrs={'class': 'listing'})
    for product in li:
     a=product.findAll('a')
     name=a[0]['data-ng-click']
     print(name)
     break


with open('wadi_sa_cat_Scrape-test.csv') as csvfile:
    reader=csv.DictReader(csvfile, delimiter=";")
    for row in reader:
        category=row['Category']
        print(category)
        url=row['Url']
        print(url)
        get_link(url,category)

