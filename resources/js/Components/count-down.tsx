import { useEffect, useRef, useState } from 'react'
import dayjs from 'dayjs'
const calculateCountdown = (date: string) => {
    let second = dayjs(date).diff(dayjs(), 'second');
    const days = Math.floor(second / (60 * 60 * 24));
    second = second % (60 * 60 * 24);
    const hours = Math.floor(second / (60 * 60));
    second = second % (60 * 60);
    const minutes = Math.floor(second / 60);
    second = second % 60;
    if (days > 0) {
        return `${days}天 ${hours}时${minutes}分${second}秒`
    }
    if (hours > 0) {
        return `${hours}时${minutes}分${second}秒`
    }
    if (minutes > 0) {
        return `${minutes}分${second}秒`
    }
    if (second > 0) {
        return `${second}秒`
    }
    return null;
}
// 生成一个倒计时组件，传入一个日期字符串 然后计算还剩多少个小时
const CountDown = (props: {
    date: string
}) => {
    const [result, setResult] = useState<string | null>(null)
    const interval = useRef<number | null>(null)
    // 通过dayjs 计算 日期与当前时间相差多少小时多少分钟 多少秒
    useEffect(() => {
        if (interval.current !== null) {
            clearInterval(interval.current)
        }
        interval.current = setInterval(() => {
            const val = calculateCountdown(props.date);
            if (!val) {
                if (interval.current !== null) {
                    clearInterval(interval.current)
                }
            }
            setResult(val)
        }, 1000)

    }, [props.date]);
    if (result) {
        return <span className='text-red-500 text-xs'>仅剩 {result}</span>
    }
    return <span className='text-gray-500 text-sm'>已下架</span>
}

export default CountDown;