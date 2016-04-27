import os
import csv
import requests
from unidecode import unidecode
from bs4 import BeautifulSoup
import math

csv_columns = ["category","link"]
currentPath = os.getcwd()
csv_file = "wadi_cat_Scrape-test.csv"
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

def get_scrape(url,cat):
	try:
		print("start")
		response = requests.get(url)
		data=list()
		var={}
		link=""
		data = response.content
		soup = BeautifulSoup(data, "html.parser")
		categorydiv = soup.find("div" , attrs={'class':'filter-module'})
		#print("categorydiv")
		main_cat=unidecode(categorydiv.find("h1").text.strip())
		categorydiv=categorydiv.find("ul" , attrs={"id":"catalog_category_option"})
		if categorydiv:
			categorydiv=categorydiv.findAll("li")
			for category in categorydiv:
				link=category.find("a")['href']
				print(unidecode(category.find("a").text.strip()))
				print(link)
				get_scrape('https://en-ae.wadi.com' + unidecode(link) , cat+"/"+main_cat)
					#print(response.content)
		else:
			var["category"]=cat+"/"+main_cat
			var['link']=url
			print(var)
			WriteDictToCSV(var)
			#data.append(var.copy())
			#WriteDictToCSV(data)
			return
	except Exception as e:
		print(str(e))
		return

	'''
def get_link(url , text):
	try:
		no_of_pages=0
		response = requests.get(url)
		data = response.content
		soup = BeautifulSoup(data, "html.parser")
		link=soup.find('div', attrs={'class' : 'options-row'})
		link=link.find('p')
		linl=link.find('strong',attrs={'class' : 'highlight'})
		if link:
			link=unidecode(link.text.strip())
			try:
				results=float(link.split(" ")[0])
				print(results)
				#no_of_pages =math.ceil(results/15)
				#print(no_of_pages)
			except Exception as e:
				print(str(e))
				return
		else:
			return;
		count=0

			get_scrape(url+'?limit='+str(results) , text)		
			
		return
	except Exception as e:
		print(str(e))
		return
		'''
get_scrape('https://en-ae.wadi.com/electronics/',"")
print ("0"+"\n")
get_scrape('https://en-ae.wadi.com/fashion-mens_footwear/',"")
print ("0"+"\n")
get_scrape('https://en-ae.wadi.com/fashion-mens_clothing/',"")
print ("0"+"\n")
get_scrape('https://en-ae.wadi.com/fashion-mens_accessories/',"")
print ("0"+"\n")
get_scrape('https://en-ae.wadi.com/fashion-women_footwear/',"")
print ("0"+"\n")
get_scrape('https://en-ae.wadi.com/fashion-women_clothing/',"")
print ("0"+"\n")
get_scrape('https://en-ae.wadi.com/fashion-womens_accessories/',"")
print ("0"+"\n")
get_scrape('https://en-ae.wadi.com/home_and_kitchen/',"")
print ("0"+"\n")
get_scrape('https://en-ae.wadi.com/automotive/',"")
print ("0"+"\n")
get_scrape('https://en-ae.wadi.com/beauty_health/',"")
print ("0"+"\n")
get_scrape('https://en-ae.wadi.com/toys_kids_and_baby/',"")
print ("0"+"\n")
