import os
import csv
import requests
from unidecode import unidecode
from bs4 import BeautifulSoup
import math
import demjson
csv_columns = ["id","name","category","image","model_number","brand","color_family","link","newprice","oldprice"]
currentPath = os.getcwd()
csv_file = "wadi_prod_scrape_sa.csv"
j=0
def WriteDictToCSV(dict_data):
	global csv_columns
	global csv_file
	global j
	try:
		with open(csv_file, 'a+') as csvfile:
			writer = csv.DictWriter(csvfile, delimiter='|', lineterminator='\n', fieldnames=csv_columns)
			if j == 0:
				writer.writeheader()
				j=1
			
			for data in dict_data:
				writer.writerow(data)
				
		return 1
	except Exception as e:
		print(str(e))
		return 0

def deep_scrape(url,index,cat):
	try:
		var={}
		datalist=list()
		response=requests.get(url)
		data=demjson.decode(response.content)
		general=data['data'][index]
		item_name=item_brand=item_newprice=item_oldprice=item_color=item_link=item_memory=item_ram=item_image=item_highlights=item_model=OS=comp=item_id=" "
		if general.get('sku'):
			item_id=general['sku']
		if general.get('name'):
			item_name=data['data'][index]['name']
		if general.get('link'):
			item_link='https://en-sa.wadi.com/' + data['data'][index]['link']
		if general['attributes'].get('color_family_en'):
			item_color=general['attributes']['color_family_en']
		if general.get('price'):
			item_oldprice=str(general['price'])
		if general.get('offerPrice'):
			item_newprice=str(general['offerPrice'])
		if general.get('internalMemory'):
			item_memory=general['internalMemory']
		if general.get('imageKey'):
			item_image='https://f.wadicdn.com/product/'+general['imageKey']+'/1-product.jpg'
		if general['attributes'].get('ram_size'):
			item_ram=general['attributes'].get('ram_size')
		# if general.get('highlights'):
		# 	for highlight in general['highlights']:
		# 		item_highlights=item_highlights + ', ' + highlight
		# 	item_highlights=item_highlights[2:]
		if general.get('brand'):
			item_brand=general['brand']['name']
		if general['attributes'].get('model_number'):
			item_model=general['attributes']['model_number']
		# if general['attributes'].get('operating_system'):
		# 	OS=general['attributes']['operating_system']
		# if general['attributes'].get('compatibility'):
		# 	comp=general['attributes']['compatibility']
		var ['category']=cat[cat.rfind("/")+1:]
		var['id']=unidecode(item_id)
		var['name']=unidecode(item_name)
		# var['model_name']=""
		# if general['attributes'].get('model_name'):
		# 	var['model_name']=unidecode(general['attributes']['model_name'])
		var['brand']=unidecode(item_brand).strip()
		var['model_number']=unidecode(item_model).strip()
		var['link']=unidecode(item_link).strip()
		var['oldprice']=unidecode(item_oldprice).strip()
		var['newprice']=unidecode(item_newprice).strip()
		var['color_family']=unidecode(item_color).strip()
		# var['internal_memory']=unidecode(item_memory).strip()
		# var['ram']=unidecode(item_ram).strip()
		# var['highlights']=unidecode(item_highlights).strip()
		var['image']=unidecode(item_image).strip()
		# var['OS']=unidecode(OS).strip()
		# var['Compatible_with']=unidecode(comp).strip()
		datalist.append(var.copy())
		WriteDictToCSV(datalist)
		#var['b']=unidecode(item_b)
		print(var['name'])
	except Exception as e:
		print(str(e))
		return
	return
def get_scrape(url,cat,count):
	try:
		print("scrapping " + url)
		response=requests.get(url)
		data=demjson.decode(response.content)
		index=0
		for index in range(int(len(data['data']))):
			print("On Page : " + count)
			deep_scrape(url,index,cat)
	except Exception as e:
		print(str(e))
		return
	return
def get_link(url , cat):
	try:
		url='https://en-sa.wadi.com/api/sawa/v1/u' + url
		#print(url)
		response = requests.get(url)
		data = demjson.decode(response.content)
		item_count=int(data['totalCount'])
		page_count=math.ceil(item_count/30)
		page_count=int(page_count)
		print ("Item count : ")
		print(item_count)
		print("Page Count : ")
		print(page_count)
		count=1
		while (page_count>=0):
			try:
				get_scrape(url + '&page=' + str(count),cat,str(count))
				count=count+1
				page_count=page_count-1
			except Exception as e:
				print(str(e))
				return
	except Exception as e:
		print(str(e))
		return
	return

with open('wadi_sa_cat_Scrape-test.csv') as csvfile:
	reader=csv.DictReader(csvfile, delimiter=";")
	for row in reader:
		get_link(row['Url'][22:],row['Category'])
		