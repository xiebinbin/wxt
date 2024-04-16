import { NavBar, Collapse, SearchBar, Toast, Divider } from 'antd-mobile'
import axios from 'axios';
import { useState } from 'react';
import { useCopyToClipboard } from 'usehooks-ts'
interface IOrder {
    code: string;
    product_cover: string;
    product_name: string;
    product_code: string;
    name: string;
    id_card: string;
    address: string;
    phone: string;
    status: 'PASSED' | 'PENDING' | 'REJECTED',
    logistics_company?: string | null;
    logistics_number?: string | null;
    reject_reason?: string | null;
    created_at: string;
    passed_at?: string;
}
const StatusText = (props: {
    status: 'PASSED' | 'PENDING' | 'REJECTED'
}) => {
    const { status } = props;
    if (status == 'PENDING') {

        return <span className='text-sm text-zinc-400'>申请中</span>
    }
    if (status == 'PASSED') {
        return <span className='text-sm text-green-400'>已发货</span>
    }
    if (status == 'REJECTED') {
        return <span className='text-sm text-red-400'>已拒绝</span>
    }
}
export default () => {
    const [orders, setOrders] = useState<IOrder[]>([])
    const [copiedText, copy] = useCopyToClipboard()
    return <>
        <NavBar onBack={() => history.back()} backArrow={true}>订单列表</NavBar>
        <div className='mt-4 px-16px'>
            <SearchBar
                placeholder='请输入收货手机'
                onSearch={val => {
                    if (/\d{11}/.test(val)) {
                        const th = Toast.show({
                            icon: 'loading',
                            content: '查询中...'
                        })
                        axios.get('/api/orders/lists', {
                            params: {
                                phone: val
                            }
                        }).then((rep) => {
                            th.close();
                            const items = rep.data.data.orders ?? [];
                            setOrders(items);
                        }).catch((e) => {
                            th.close()
                            console.log(e);
                        })
                    } else {
                        Toast.show({
                            icon: 'fail',
                            content: '手机号不正确',
                            duration: 1000
                        })
                    }
                }}
                clearable
            />
        </div>
        <div className='mt-4 py-16px'>
            <Collapse>
                {orders.map((order) =>
                    <Collapse.Panel key={order.code} title={<div className='w-full flex justify-between'>
                        <div className='flex items-center'>
                            <img className='h-18px mr-5px' src={order.product_cover} alt="" />
                            <span>{order.product_name}</span>
                        </div>
                        <StatusText status={order.status} />
                    </div>}>
                        <div className='w-full'>
                            <div className='flex justify-between items-center'>
                                <span className='text-sm'><span className='text-zinc-700'>套餐</span>：{order.product_name}</span>
                                <span onClick={() => {
                                    location.href = '/products/' + order.product_code;
                                }} className='text-xs text-blue-600'>查看详情</span>
                            </div>
                            <Divider />
                            <div className='flex items-center'>
                                <span className='text-sm text-zinc-700'>姓名：</span>
                                <span className='text-xs'>{order.name}</span>
                            </div>
                            <Divider />
                            <div className='flex items-center'>
                                <span className='text-sm text-zinc-700'>身份证：</span>
                                <span className='text-xs'>{order.id_card}</span>
                            </div>
                            <Divider />
                            <div className='flex items-center'>
                                <span className='text-sm text-zinc-700'>收货电话：</span>
                                <span className='text-xs'>{order.phone}</span>
                            </div>
                            <Divider />
                            <div className='flex items-center'>
                                <span className='text-sm text-zinc-700'>收货地址：</span>
                                <span className='text-xs'>{order.address}</span>
                            </div>
                            <Divider />
                            <div className='flex items-center'>
                                <span className='text-sm text-zinc-700'>订单状态：</span>
                                <StatusText status={order.status} />
                            </div>
                            <Divider />
                            <div className='flex items-center'>
                                <span className='text-sm text-zinc-700'>快递公司：</span>
                                <span className='text-xs'>{order.logistics_company}</span>
                            </div>
                            <Divider />
                            <div className='flex justify-between items-center'>
                                <span className='text-sm'><span className='text-zinc-700'>快递单号：</span>{order.logistics_number}</span>
                                <span className='text-sm text-zinc-700'></span>
                                <span onClick={() => {
                                    copy(order.logistics_number ?? '')
                                        .then(() => {
                                            Toast.show({
                                                icon: 'success',
                                                content: '复制成功'
                                            })
                                        })
                                        .catch(error => {
                                            Toast.show({
                                                icon: 'fail',
                                                content: '复制失败'
                                            })
                                        })
                                }} className='text-xs text-blue-600'>点击复制</span>
                            </div>
                            {order.status == 'REJECTED' ? <>
                                <Divider />
                                <div className='flex items-center'>
                                    <span className='text-sm text-zinc-700'>拒绝原因：</span>
                                    <span className='text-xs'>{order.reject_reason}</span>
                                </div>
                            </> : null}
                            <Divider />
                            <div className='flex items-center'>
                                <span className='text-sm text-zinc-700'>申请时间：</span>
                                <span className='text-xs'>{order.created_at}</span>
                            </div>
                            {order.status == 'PASSED' ? <>
                                <Divider />
                                <div className='flex items-center'>
                                    <span className='text-sm text-zinc-700'>发货时间：</span>
                                    <span className='text-xs'>{order.passed_at}</span>
                                </div>
                            </> : null}
                        </div>
                    </Collapse.Panel>
                )}
            </Collapse>
        </div>
    </>
}