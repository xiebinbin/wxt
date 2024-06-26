import { Button, Image, Swiper, Tabs } from 'antd-mobile'
import logoWebp from '@/assets/logo.webp'
import orderWebp from '@/assets/order.webp'
import chatWebp from '@/assets/chat.webp'
import queryWebp from '@/assets/query.webp'
import cuWebp from '@/assets/cu.webp'
import cmWebp from '@/assets/cm.webp'
import ctWebp from '@/assets/ct.webp'
import cbWebp from '@/assets/cb.webp'
import { useEffect } from 'react'
import CountDown from '@/Components/count-down'
interface IProductListItem {
    code: string;
    title: string;
    description?: string;
    badge?: string;
    apply_count: number;
    list_cover: string;
    expired_at: string;
}
interface IProps {
    tags: string[]
    code?: string | null;
    banners: { img: string, url: string }[]
    currentTag: string
    products: IProductListItem[]
}

const ProductItem = (props: {
    item: IProductListItem
}) => {
    const { item } = props;
    return <div onClick={() => {
        location.href = "/products/" + item.code;
    }} className='bg-gray-100 w-full flex rounded-10px justify-between mt-10px'>
        <div className='w-20% justify-center flex items-center pl-10px'>
            <Image
                src={item.list_cover}
                fit='cover'
                width={50}
            />
        </div>
        <div className='w-55% flex flex-col py-5px'>
            <span className='text-sm text-zinc-600'>{item.title}</span>
            <span className='text-xs text-zinc-400 pb-5px'>{item.description}</span>
            <span className='text-xs text-zinc-400 pb-10px'>{item.apply_count}+人已办理</span>
            <CountDown date={item.expired_at} />
        </div>
        <div className='w-25% flex flex-col justify-between pb-5px'>
            <div className='flex justify-end'>
                {item.badge ? <div className='text-xs inline-block rounded-tr-10px rounded-bl-10px bg-red-500 p-2px text-white font-bold'>
                    {item.badge}
                </div> : null}
            </div>
            <div className='mr-5px flex justify-end'>
                <Button size='mini' color='primary' shape='rounded'>马上办理</Button>
            </div>
        </div>
    </div>
}
export default ({ tags, currentTag, banners, products, code }: IProps) => {
    useEffect(() => {
        if (code) {
            localStorage.setItem('code', code)
        } else {
            const old = localStorage.getItem('code')
            if (!old) {
                localStorage.setItem('code', 'S5u2');
            }
        }
    }, [])
    return <div>
        <div className='h-44px w-full flex items-center'>
            <img className='h-30px ml-4' src={logoWebp} />
        </div>
        <div>
            <Swiper autoplay={true}>
                {banners.map((banner, index) => (
                    <Swiper.Item key={index}>
                        <div className='bg-gray-700' onClick={() => window.open(banner.url)} >
                            <img className='w-full' src={banner.img} />
                        </div>
                    </Swiper.Item>
                ))}
            </Swiper>
        </div>
        <div className='w-full flex justify-center my-2'>
            <div className='w-60% flex justify-between'>
                <div onClick={() => {
                    location.href = '/orders/index'
                }} className='w-1/4 flex items-center flex-col'>
                    <img className='w-full' src={orderWebp} />
                    <small className='text-gray-400'>我的订单</small>
                </div>
                <div onClick={() => {
                    location.href = '/customer?code=' + (localStorage.getItem('code') ?? 'S5u2')
                }} className='w-1/4 flex items-center flex-col'>
                    <img className='w-full' src={chatWebp} />
                    <small className='text-gray-400'>在线客服</small>
                </div>
                <div className='w-1/4 flex items-center flex-col' onClick={() => window.open('https://getsimnum.caict.ac.cn/m/#/')}>
                    <img className='w-full' src={queryWebp} />
                    <small className='text-gray-400'>名下电话卡</small>
                </div>
            </div>
        </div>
        <div className='w-full flex flex-col items-center justify-center my-1 mt-4'>
            <div className='w-5/7 flex justify-center items-center'>
                <span className='text-sm text-zinc-700 font-bold'>合作伙伴</span>
            </div>
            <div className='w-5/7 flex justify-between'>
                <div className='flex flex-col items-center'>
                    <img className='w-30px' src={cuWebp} />
                    <span className='text-xs text-zinc-400'>中国联通</span>
                </div>
                <div className='flex flex-col items-center'>
                    <img className='w-30px' src={cmWebp} />
                    <span className='text-xs text-zinc-400'>中国移动</span>
                </div>
                <div className='flex flex-col items-center'>
                    <img className='w-30px' src={ctWebp} />
                    <span className='text-xs text-zinc-400'>中国电信</span>
                </div>
                <div className='flex flex-col items-center'>
                    <img className='w-30px' src={cbWebp} />
                    <span className='text-xs text-zinc-400'>中国广电</span>
                </div>
            </div>
        </div>
        <div className='w-full'>
            <Tabs defaultActiveKey={currentTag} onChange={(v) => {
                location.href = `/home/index?tag=` + v
            }}>
                {tags.map((tag) => <Tabs.Tab style={{
                    '--title-font-size': '14px',
                }} title={tag} key={tag} />)}
            </Tabs>
        </div>
        <div className='w-full px-16px pb-30px'>
            {products.map(product => <ProductItem key={product.code} item={product} />)}
        </div>
    </div>
}