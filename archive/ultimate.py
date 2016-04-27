import os
import csv
import requests
from unidecode import unidecode
from bs4 import BeautifulSoup
import math
import sys

csv_columns = ["url","name","img","newprice",  "oldprice","category","brand"]
currentPath = os.getcwd()
csv_file = "ultimate.csv"
j=0
def WriteDictToCSV(data):
	global csv_columns
	global csv_file
	global j
	try:
		with open(csv_file, 'a+') as csvfile:
			writer = csv.DictWriter(csvfile, delimiter=';', lineterminator='\n', fieldnames=csv_columns)
			if j == 0:
				writer.writeheader()
				j=1
			writer.writerow(data)
		return 1
	except Exception as e:
		print(str(e))
		return 0

def get_scrape(url):
	try:
		print(url)
		no_of_pages=0
		response = requests.get(url)
		data = response.content
		soup = BeautifulSoup(data, "html.parser")
		data=list()
		list_thumb = soup.findAll("div" , attrs={'class':'placard'})
		#print(list_thumb)
		for product in list_thumb:
			image_div =product.find("div" , attrs={"class":"small-5"})
			if not image_div:
				image_div=product.findAll("div", attrs={"class":"small-12"})
				image_div=image_div[0]
			name_div=product.findAll("div", attrs={"class": "row"})
			name_div=name_div[0]
			name=name_div.findAll("a",attrs={"class":"itemLink"})
			url=name[0]['href']
			name=name[0]['title']
			details_div=product.find("div" , attrs={"class":"small-7"})
			image=names=oldprice=newprice=""
			if image_div:
				image=image_div.find("img")
			if not details_div:
				details_div=product.findAll("div",attrs={"class":"small-12"})
				details_div=details_div[1]
			oldprice = details_div.find("span" , attrs= {'class':'was'})
			newprice = details_div.find("span" , attrs= {'class':'is'})
			if not oldprice or oldprice.text=="":
				oldprice=newprice
			#color,os,os_v,ram,storage,model,comp=deep_scrape(name['href'])
			var={}
			try:
				if image.get('data-src'):
					var['img']=unidecode(image['data-src'])
				else:
					var['img']=unidecode(image['src'])
				var['name'] = unidecode(name.strip())
				# print("name: " + name)
				var['url']=unidecode(url)
				var['oldprice']=unidecode(oldprice.text.strip())
				var['newprice']=unidecode(newprice.text.strip())
				print(var)
				# var ['category']=unidecode(text)
				# var['brand']=unidecode(brand)
				# var['brand']=unidecode(brand)
				# var['color']=unidecode(color)
				# var['os']=unidecode(os)
				# var['os_version']=unidecode(os_v)
				# var['ram']=unidecode(ram)
				# var['storage']=unidecode(storage)
				# var['model_number']=unidecode(model)
				# var['compatible_with']=unidecode(comp)
				#print(var)
				WriteDictToCSV(var)
			except Exception as e:
				print(str(e))
				
	except Exception as e:
		print(str(e))
		return
fo = open("ultimate.csv", "rw+")
fo.truncate()
fo.close()
url=sys.argv[1]
# print("Before replace\n" + name + "\n")
# name=name.replace(" ","-")
# print("After replace\n" + name)
print(url)
response = requests.get(url)
data = response.content
soup = BeautifulSoup(data, "html.parser")
link=soup.find('div', attrs={'class' : 'listing-page-text'})
if link:
	link=unidecode(link.text.strip())
	try:
		results=float(link.split(" ")[0])
		print("Total results : ")
		print(results)
		no_of_pages =math.ceil(results/15)
		print("No. of pages : ")
		print(no_of_pages)
		count=0
		while(no_of_pages>0):
			count=count+1
			no_of_pages-=1
			get_scrape(url+'?sortby=cp_asc&page=1')
			break
	except Exception as e:
		print(str(e))


