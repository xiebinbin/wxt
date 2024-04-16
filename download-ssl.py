import os,sys,requests
def main():
    # 从终端参数重接收一个url
    url = sys.argv[1]
    dest_path = '/data/ssl'
    if not os.path.exists(dest_path):
        os.makedirs(dest_path)
    # 从url中获取文件名
    file_name = url.split('/')[-1]
    dest_file = os.path.join(dest_path, file_name)
    # 下载文件
    response = requests.get(url, verify=False, stream=True)
    if response.status_code == 200:
        if os.path.exists(dest_file):
            os.unlink(dest_file)
        with open(dest_file, "wb") as f:
            for chunk in response.iter_content(chunk_size=8192):
                f.write(chunk)
            print(f"文件已成功下载到目录：{dest_file}")
            # 重启nginx
            os.system("systemctl restart nginx")
            print("nginx已成功重启。")
    else:
        print("下载失败，请检查URL或网络连接。")
    pass
if __name__ == "__main__":
    main()