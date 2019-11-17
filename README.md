# LIHKG Proxy #
> 一個用嚟上[連登](https://lihkg.com/)既代理伺服器 (Proxy)

## 簡介 ##
係觀塘某個共享辦工室返工，唯一能夠使用既（免費）上網方法係場地提供既公廁 Wi-Fi。好不幸地個 IP Address 俾連登 block 左⋯⋯

![Screen Preview](https://github.com/icelam/lihkg-proxy/raw/master/docs/cloudflare.png)

雖則話用 VPN 能夠解決，但連接公司 dev 場果陣又要熄左佢，實在係太麻煩。所以設計左呢個簡易 proxy。

## 已知限制 / 缺點 ##
* 不能登入，只能做 CD-ROM（未諗到點處理 Google Recaptcha）
* 不能註冊（都係 Google Recaptcha 問題）
* 未處理分享回應 / 主題時既 share ID： 已對分享功能做暫時性 patching，但已分享既連結部份可能唔 work。（已搵到生成 share ID 既 function，要少少時間做 reverse engineering）

## 注意事項 ##
強烈建議咁多位巴打絲打：
1. 如果 IP 冇被 block 建議用返連登官方網頁 [https://lihkg.com/](https://lihkg.com/)。  
2. 如有需要，請盡量自己搵個免費 hosting host 左佢，以免太多流量令 proxy server 被 block。[Deploy 教學](https://github.com/icelam/lihkg-proxy/blob/master/docs/DEPLOYMENT.md)

---

利申：Front-end 狗一名，唔熟 backend。只供教學用途。  
[Demo Link](https://lihkg-proxy.herokuapp.com/)