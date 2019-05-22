# -*- coding:utf-8 -*-
import requests
# from bs4 import BeautifulSoup
# from urllib.request import urlopen
import pymysql

def get_stockinfo(market):
    header = {'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36'}
    header = {'User-Agent': 'Chrome/66.0.3359.181'}
    header = {'User-Agent': 'Mozilla/5.0', 'referer': 'http://www.daum.net'}

    res = requests.get(
        'http://finance.daum.net/api/trend/price_performance?page=1&perPage=30&intervalType=TODAY&changeType=UPPER_LIMIT&pagination=true&order=desc&market=' + market,
        headers=header)
    res_json = res.json()
    market_data = []

    if len(res_json['data']) > 0:
        market += ' : '
        for data in res_json['data']:
            market_data.append(data['name'] + '(' + str(round(data['changeRate']*100,2)) + '%,' + str( format(int(data['tradePrice']),',') ) + '원)')

        return market + ','.join(market_data)
    else:
        return ''

def main():
    stockinfo = []
    stockinfo.append(get_stockinfo('KOSPI'))
    stockinfo.append(get_stockinfo('KOSDAQ'))

    today_stock = ', '.join(stockinfo)

    if len(today_stock) > 5:
        db = pymysql.connect(host='localhost', port=3306, user='root', passwd='******', db='slim_db', charset='utf8')
        cur = db.cursor()
        sql = "insert into stocks_history (items) values (%s)"
        cur.execute(sql, today_stock)
        db.commit()
        cur.close()
        db.close()

if __name__ == '__main__':
    main()
