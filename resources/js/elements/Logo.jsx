const Logo = () => {

    function home() {
        return window.location.replace('/')
    }

    return (
        <div className="nv-logo">
            <svg className="nv-logo-path" xmlns="http://www.w3.org/2000/svg" width="115" height="50"
                 viewBox="0 0 129.682 70">
                <path id="Path_1" data-name="Path 1" onClick={() => {
                    home()
                }}
                      d="M483.165,1644.518v29.513a34.861,34.861,0,0,1-69.722-.126,24.406,24.406,0,0,0-26.581-24.308c-12.255,1.062-22.224,12.578-22.224,24.879v29.036a5.3,5.3,0,0,1-3.044,4.866,5.621,5.621,0,0,1-8.112-5.016v-28.706c0-17.457,14.14-33.843,31.523-35.456a34.883,34.883,0,0,1,38.2,34.705,24.4,24.4,0,0,0,48.805.07v-29.607a5.3,5.3,0,0,1,3.043-4.866A5.621,5.621,0,0,1,483.165,1644.518Z"
                      transform="translate(-353.483 -1638.94)" fill="#333344"/>
            </svg>
        </div>
    )
}

export default Logo
