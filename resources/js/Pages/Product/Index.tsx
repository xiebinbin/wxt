import { Button, CascadePicker, CascadePickerOption, CascadePickerRef, Dialog, Form, Input, Modal, NavBar, Popup, Toast } from 'antd-mobile';
import CountDown from '@/Components/count-down'
import { useEffect, useRef, useState } from 'react';
import dayjs from 'dayjs';
import policeWebp from '@/assets/police.webp'
import regionsJson from '@/assets/regions.json'
import axios from 'axios';
interface ProductInfoProps {
    product: {
        code: string;
        title: string;
        cover: string;
        expired_at: string;
        badge: string;
        apply_count: number;
        reminder: string;
        description: string;
        monthly_rent: number;
        monthly_rent_description: string;
        traffic: number;
        traffic_description: string;
        call_description: string;
        discount_description: string;
        rent_introduction: string;
    };
}
const getRegionsTree = (regions: any) => {
    // 循环regionsJson
    const items: CascadePickerOption[] = [];
    for (const key in regions) {
        const element: any = regions[key];
        if (typeof element === 'object') {
            items.push({
                label: key,
                value: key,
                children: getRegionsTree(element)
            });
        } else {
            items.push({
                label: element,
                value: element
            })
        }
    }
    return items;
}
export default ({ product }: ProductInfoProps) => {
    const [submitState, setSubmitState] = useState(true);
    const [submitPopupState, setSubmitPopupState] = useState(false);
    const [regionsTree, setRegionsTree] = useState<CascadePickerOption[]>([]);
    const cascadePickerRef = useRef<CascadePickerRef>(null);
    const [form] = Form.useForm()
    const [address, setAddress] = useState('');
    useEffect(() => {
        setRegionsTree(getRegionsTree(regionsJson));
        const currentTime = dayjs();
        const expiredTime = dayjs(product.expired_at);
        if (currentTime.isAfter(expiredTime)) {
            setSubmitState(false);
        }

        const modal = Modal.show({
            bodyClassName: '!bg-blue-700',
            title: <div className='flex items-center justify-center'><span className='text-white font-bold mr-5px'>温馨提示</span><img className='w-16px' src={policeWebp} /></div>,
            content: <div>
                <div>
                    <span className='text-white text-sm'>公安部提示:请勿将已登记您本人身份证信息的号卡用于诈骗等不合法行为，请勿将号卡进行转实、租借他人使用，请及时挂失、注销已丢失号卡，保护个人信息安全。</span>
                    <span className='text-yellow-500 text-sm'>涉嫌诈骗等违法犯罪行为等号码，实名登记机主需承担法律宽任。</span>
                </div>
                <Button onClick={() => {
                    modal.close()
                }} className='!text-blue-700 !mt-10px' block shape='rounded' size='small'>我已知晓，并承诺本人使用</Button>
            </div>,
        })
    }, [])
    const onSubmit = async () => {
        form.validateFields().then(() => {
            const values = {
                ...form.getFieldsValue(),
                product_code: product.code,
                code: localStorage.getItem('code')
            }
            const th = Toast.show({
                icon: 'loading',
                content: '提交中',
                maskClickable: false,
            })
            axios.post('/api/orders',values).then(() => {
                th.close();
                Toast.show({
                    'icon':'success',
                    content: '申请成功',
                    duration: 2000,
                })
                setSubmitPopupState(false);
            }).catch(() => {
                th.close();
                Toast.show({
                    'icon':'fail',
                    content: '申请失败,请重试！',
                    duration: 2000,
                })
            })
        });

    }
    return <>
        <NavBar onBack={() => history.back()} backArrow={true}>套餐详情</NavBar>
        <div className='w-screen pb-50px'>
            <img className='w-full h-200px' src={product.cover} alt="" />
            <div className='w-full px-16px'>
                <div className='flex'>
                    <div className='w-72%'>
                        <div>
                            <span className='text-xs inline-block rounded-tr-10px rounded-bl-10px bg-red-500 p-2px text-white font-bold mr-5px'>{product.badge}</span>
                            <span className='text-sm'>{product.title}</span>
                        </div>
                        <div className='text-xs text-gray-400'>
                            <span>{product.description}</span>
                        </div>
                        <div>
                            <CountDown date={product.expired_at} />
                        </div>
                    </div>
                    <div className='w-28%'>
                        <div className='flex items-end justify-end'>
                            <span className='text-xs text-zinc-400 pb-10px'>{product.apply_count}+人已办理</span>
                        </div>
                    </div>
                </div>
                <div className='w-full mt-15px'>
                    <table className='rounded-15px overflow-hidden w-full'>
                        <thead className='bg-blue-500 text-white'>
                            <th className='p-2px'>月租</th>
                            <th className='p-2px'>全国流量</th>
                            <th className='p-2px'>通话</th>
                            <th className='p-2px'>优惠</th>
                        </thead>
                        <tbody className='bg-zinc-100'>
                            <tr>
                                <td className='text-center'>
                                    <div>
                                        <span className='text-red-500 font-bold text-base'>{product.monthly_rent}</span>
                                        <span className='text-zinc-300 text-xs'>元/月</span>
                                    </div>
                                    <div>
                                        <span className='text-zinc-300 text-xs'>{product.monthly_rent_description}</span>
                                    </div>
                                </td>
                                <td className='flex items-center flex-col justify-center'>
                                    <div>
                                        <span className='text-red-500 font-bold text-base'>{product.traffic}G</span>
                                    </div>
                                    <div>
                                        <span className='text-zinc-300 text-xs'>{product.traffic_description}</span>
                                    </div>
                                </td>
                                <td>
                                    <div className='flex items-center justify-center'>
                                        <span className='text-red-500 text-xs'>{product.call_description}</span>
                                    </div>
                                </td>
                                <td className='w-20% p-5px'>
                                    <span className='text-red-500 font-bold text-sm text-center'>{product.discount_description}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div className='mt-15px'>
                    <div className='flex'>
                        <span className='w-3px bg-blue-500 mr-5px'></span>
                        <span className='text-base font-bold'>资费介绍</span>
                    </div>
                    <div dangerouslySetInnerHTML={{ __html: product.rent_introduction }}></div>
                </div>
                <div className='mt-15px'>
                    <div className='flex'>
                        <span className='w-3px bg-blue-500 mr-5px'></span>
                        <span className='text-base font-bold'>温馨提示</span>
                    </div>
                    <div dangerouslySetInnerHTML={{ __html: product.reminder }}></div>
                </div>
            </div>
            <div className='w-screen fixed bottom-0 left-0 px-16px py-5px'>
                <Button onClick={() => {
                    form.resetFields()
                    setAddress('')
                    setSubmitPopupState(true)
                }} disabled={!submitState} block color='primary' shape='rounded' size='middle'>
                    立即申请
                </Button>
            </div>
            <Popup
                visible={submitPopupState}
                onMaskClick={() => {
                    setSubmitPopupState(false)
                }}
                onClose={() => {
                    setSubmitPopupState(false)
                }}
            >
                <Form form={form} footer={
                    <Button onClick={() => {
                        onSubmit()
                    }} block type='submit' shape='rounded' color='primary' size='large'>
                        确认提交
                    </Button>
                } layout='horizontal'>
                    <Form.Header>填写办理人信息</Form.Header>
                    <Form.Item rules={[
                        {
                            required: true,
                            message: '请输入真实姓名'
                        }
                    ]} label='姓名' name='name'>
                        <Input placeholder='请输入真实姓名' clearable />
                    </Form.Item>
                    <Form.Item rules={[
                        {
                            required: true,
                            message: '请输入身份证号'
                        },
                        { pattern: /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X)$)/, message: '请输入正确的身份证号' }
                    ]} label='身份证' name='id_card'>
                        <Input placeholder='请输入身份证号' clearable />
                    </Form.Item>
                    <Form.Item rules={[
                        {
                            required: true,
                            message: '请输入手机号'
                        },
                        {
                            pattern: /^1[3-9]\d{9}$/, message: '请输入正确的手机号'
                        }
                    ]} label='收货手机' name='phone'>
                        <Input placeholder='请输入收货手机号' clearable />
                    </Form.Item>
                    <Form.Item
                        name='address'
                        label='地址'
                        trigger='onConfirm'
                        rules={[{ required: true, message: '请选择地址' }]}
                        onClick={() => cascadePickerRef.current?.open()}
                    >
                        <div>
                            <CascadePicker
                                ref={cascadePickerRef}
                                title='选择地区'
                                options={regionsTree}
                                onConfirm={(val) => {
                                    form.setFieldsValue({ address: val.join('') })
                                    setAddress(form.getFieldValue('address'))
                                }}
                            />
                            <span>{address}</span>
                        </div>
                    </Form.Item>
                    <Form.Item rules={[{
                        required: true,
                        message: '请输入详细地址'
                    }]} label='详细地址' name='address_detail'>
                        <Input placeholder='请输入收货详细地址' clearable />
                    </Form.Item>
                </Form>
            </Popup>
        </div>
    </>
}