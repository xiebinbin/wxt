import { NavBar } from 'antd-mobile';
interface CustomerProps {
    qrcode: string | null;
}
export default ({qrcode}: CustomerProps) => {
    return <>
        <NavBar onBack={() => history.back()} backArrow={true}>联系客服</NavBar>
        <div className='w-screen'>
           {qrcode ? <img className='w-full' src={qrcode} alt="" /> : null}
        </div>
    </>
}