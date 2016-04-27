import os
import csv
import requests
from unidecode import unidecode
from bs4 import BeautifulSoup
import math
import sys
import MySQLdb	

def WriteDictToDb(data,cat):
	sql="INSERT INTO lookups_sa(Device_ID,souq_url,souq_name,souq_img,souq_oldprice,souq_newprice) VALUES  (%(id)s, %(url)s, %(name)s, %(img)s, %(old)s, %(new)s)"
	data = {'id':data['ID'], 'url':data['url'], 'name':data['name'], 'img':data['img'], 'old': data['oldprice'], 'new':data['newprice']}
	try:
		cursor.execute(sql,data)
		db.commit()
	except Exception as e:
		print(str(e))
		pass
def get_scrape(url,res,cat,wid):
	print("Storing in db")
	try:
		print(url)
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
				var['ID']=wid
				var ['category']=cat
				WriteDictToDb(var,cat)
				
			except Exception as e:
				print(str(e))
				
	except Exception as e:
		print(str(e))
		return
	return
def get_link(name,cat,wid):
	wadi=name
	name=name.lower()
	name=name.replace(" ","-")
	if "(" in name:
		name=name[0:name.index("(")]
		name=name.strip()
	if "," in name:
		name=name[0:name.index(",")]
		name=name.strip()
	url="http://uae.souq.com/ae-en/"+name+"/s"
	print(url)
	response = requests.get(url)
	if response.history:
	    # print "Request was redirected"
	    # for resp in response.history:
	    #     print resp.status_code, resp.url
	    # print "Final destination:"
	    # print response.status_code, response.url
	    furl=response.url;
	    print("final :" + furl + '\n')
	else:
	    print ("Request was not redirected")
	    furl=url
	    print(furl + '\n')
	response=requests.get(furl)
	data = response.content
	soup = BeautifulSoup(data, "html.parser")
	link=soup.find('div', attrs={'class' : 'listing-page-text'})
	if link:
		link=unidecode(link.text.strip())
		results=float(link.split(" ")[0])
		print("Total results : ")
		print(results)
		get_scrape(furl,results,cat,wid)
	else:
		print("\nNothing found")
		return

db=MySQLdb.connect('localhost','root','jlabs@123','zadmin_wadi')
cursor=db.cursor()
cursor.execute('select * from wadi_sa')
res=cursor.fetchall()

for row in res:
	if(row[0]<=228):
		continue
  	else:
		print("ID: " + str(row[0]))
		get_link(row[2],row[3],row[1])
	


