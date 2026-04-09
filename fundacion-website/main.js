import * as THREE from 'three';

// Scene setup
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });

renderer.setSize(window.innerWidth, window.innerHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
document.getElementById('canvas-container').appendChild(renderer.domElement);

// Color palette - magical and hopeful
const colors = {
    pink: 0xFF69B4,
    gold: 0xFFD700,
    purple: 0x9C27B0,
    blue: 0x2196F3,
    cyan: 0x00BCD4,
    magenta: 0xE91E63,
    orange: 0xFF9800,
    white: 0xFFFFFF
};

// Create magical particles
const particlesGeometry = new THREE.BufferGeometry();
const particlesCount = 3000;

const posArray = new Float32Array(particlesCount * 3);
const colorArray = new Float32Array(particlesCount * 3);
const sizeArray = new Float32Array(particlesCount);
const speedArray = new Float32Array(particlesCount);

const colorPalette = [colors.pink, colors.gold, colors.purple, colors.cyan, colors.magenta, colors.orange];

for (let i = 0; i < particlesCount * 3; i += 3) {
    // Position - spread across the scene
    posArray[i] = (Math.random() - 0.5) * 50;
    posArray[i + 1] = (Math.random() - 0.5) * 50;
    posArray[i + 2] = (Math.random() - 0.5) * 50;

    // Random colors from palette
    const randomColor = new THREE.Color(colorPalette[Math.floor(Math.random() * colorPalette.length)]);
    colorArray[i] = randomColor.r;
    colorArray[i + 1] = randomColor.g;
    colorArray[i + 2] = randomColor.b;

    // Random sizes
    sizeArray[i / 3] = Math.random() * 0.5 + 0.1;
    
    // Random speeds
    speedArray[i / 3] = Math.random() * 0.02 + 0.005;
}

particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
particlesGeometry.setAttribute('color', new THREE.BufferAttribute(colorArray, 3));
particlesGeometry.setAttribute('size', new THREE.BufferAttribute(sizeArray, 1));

// Custom shader material for particles
const particlesMaterial = new THREE.PointsMaterial({
    size: 0.3,
    vertexColors: true,
    transparent: true,
    opacity: 0.8,
    blending: THREE.AdditiveBlending,
    depthWrite: false
});

const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
scene.add(particlesMesh);

// Create floating geometric shapes (representing hope and dreams)
const shapes = [];
const geometries = [
    new THREE.IcosahedronGeometry(1, 0),
    new THREE.OctahedronGeometry(1, 0),
    new THREE.TetrahedronGeometry(1, 0),
    new THREE.TorusGeometry(0.7, 0.3, 8, 16),
    new THREE.DodecahedronGeometry(0.8, 0)
];

for (let i = 0; i < 15; i++) {
    const geometry = geometries[Math.floor(Math.random() * geometries.length)];
    const material = new THREE.MeshPhongMaterial({
        color: colorPalette[Math.floor(Math.random() * colorPalette.length)],
        shininess: 100,
        specular: 0x444444,
        transparent: true,
        opacity: 0.7,
        wireframe: Math.random() > 0.5
    });

    const mesh = new THREE.Mesh(geometry, material);
    
    mesh.position.x = (Math.random() - 0.5) * 30;
    mesh.position.y = (Math.random() - 0.5) * 30;
    mesh.position.z = (Math.random() - 0.5) * 20 - 10;
    
    mesh.rotation.x = Math.random() * Math.PI;
    mesh.rotation.y = Math.random() * Math.PI;
    
    const scale = Math.random() * 1.5 + 0.5;
    mesh.scale.set(scale, scale, scale);
    
    mesh.userData = {
        rotationSpeed: {
            x: (Math.random() - 0.5) * 0.02,
            y: (Math.random() - 0.5) * 0.02
        },
        floatSpeed: Math.random() * 0.5 + 0.5,
        floatOffset: Math.random() * Math.PI * 2,
        originalY: mesh.position.y
    };
    
    shapes.push(mesh);
    scene.add(mesh);
}

// Create a central heart shape (symbol of love and care)
const heartShape = new THREE.Shape();
const x = 0, y = 0;
heartShape.moveTo(x + 0.5, y + 0.5);
heartShape.bezierCurveTo(x + 0.5, y + 0.5, x + 0.4, y, x, y);
heartShape.bezierCurveTo(x - 0.6, y, x - 0.6, y + 0.7, x - 0.6, y + 0.7);
heartShape.bezierCurveTo(x - 0.6, y + 1.1, x - 0.3, y + 1.54, x + 0.5, y + 1.9);
heartShape.bezierCurveTo(x + 1.2, y + 1.54, x + 1.6, y + 1.1, x + 1.6, y + 0.7);
heartShape.bezierCurveTo(x + 1.6, y + 0.7, x + 1.6, y, x + 1.0, y);
heartShape.bezierCurveTo(x + 0.7, y, x + 0.5, y + 0.5, x + 0.5, y + 0.5);

const heartExtrudeSettings = {
    steps: 2,
    depth: 0.5,
    bevelEnabled: true,
    bevelThickness: 0.1,
    bevelSize: 0.1,
    bevelSegments: 3
};

const heartGeometry = new THREE.ExtrudeGeometry(heartShape, heartExtrudeSettings);
const heartMaterial = new THREE.MeshPhongMaterial({
    color: colors.pink,
    shininess: 150,
    specular: 0xff69b4,
    transparent: true,
    opacity: 0.9,
    emissive: 0xff1493,
    emissiveIntensity: 0.3
});

const heartMesh = new THREE.Mesh(heartGeometry, heartMaterial);
heartMesh.scale.set(1.5, 1.5, 1.5);
heartMesh.position.z = -5;
heartMesh.rotation.z = Math.PI;
scene.add(heartMesh);

// Create wireframe sphere around heart
const sphereGeometry = new THREE.SphereGeometry(4, 32, 32);
const sphereMaterial = new THREE.MeshBasicMaterial({
    color: colors.gold,
    wireframe: true,
    transparent: true,
    opacity: 0.3
});
const wireframeSphere = new THREE.Mesh(sphereGeometry, sphereMaterial);
wireframeSphere.position.z = -5;
scene.add(wireframeSphere);

// Create ribbon/twist shapes (representing journey and hope)
const ribbons = [];
for (let i = 0; i < 5; i++) {
    const curve = new THREE.CatmullRomCurve3([
        new THREE.Vector3(-15 + Math.random() * 30, -10, -15),
        new THREE.Vector3(-10 + Math.random() * 20, -5, -10),
        new THREE.Vector3(-5 + Math.random() * 10, 0, -5),
        new THREE.Vector3(0 + Math.random() * 10, 5, 0),
        new THREE.Vector3(5 + Math.random() * 10, 10, 5),
        new THREE.Vector3(10 + Math.random() * 10, 15, 10),
        new THREE.Vector3(15 + Math.random() * 10, 20, 15)
    ]);

    const ribbonGeometry = new THREE.TubeGeometry(curve, 64, 0.15, 8, false);
    const ribbonMaterial = new THREE.MeshPhongMaterial({
        color: colorPalette[i % colorPalette.length],
        shininess: 100,
        transparent: true,
        opacity: 0.6
    });

    const ribbon = new THREE.Mesh(ribbonGeometry, ribbonMaterial);
    ribbon.userData = {
        curve: curve,
        phase: Math.random() * Math.PI * 2
    };
    
    ribbons.push(ribbon);
    scene.add(ribbon);
}

// Lighting
const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
scene.add(ambientLight);

const pointLight1 = new THREE.PointLight(colors.pink, 2, 100);
pointLight1.position.set(10, 10, 10);
scene.add(pointLight1);

const pointLight2 = new THREE.PointLight(colors.gold, 2, 100);
pointLight2.position.set(-10, -10, 10);
scene.add(pointLight2);

const pointLight3 = new THREE.PointLight(colors.purple, 1.5, 100);
pointLight3.position.set(0, 15, -10);
scene.add(pointLight3);

// Camera position
camera.position.z = 15;

// Mouse interaction
let mouseX = 0;
let mouseY = 0;
let targetX = 0;
let targetY = 0;

const windowHalfX = window.innerWidth / 2;
const windowHalfY = window.innerHeight / 2;

document.addEventListener('mousemove', (event) => {
    mouseX = (event.clientX - windowHalfX) * 0.001;
    mouseY = (event.clientY - windowHalfY) * 0.001;
});

// Touch interaction for mobile
document.addEventListener('touchmove', (event) => {
    if (event.touches.length > 0) {
        mouseX = (event.touches[0].clientX - windowHalfX) * 0.002;
        mouseY = (event.touches[0].clientY - windowHalfY) * 0.002;
    }
});

// Scroll interaction
let scrollY = 0;
window.addEventListener('scroll', () => {
    scrollY = window.scrollY;
});

// Animation
const clock = new THREE.Clock();

function animate() {
    requestAnimationFrame(animate);
    
    const elapsedTime = clock.getElapsedTime();
    
    // Smooth mouse movement
    targetX += (mouseX - targetX) * 0.05;
    targetY += (mouseY - targetY) * 0.05;
    
    // Rotate entire particle system based on mouse
    particlesMesh.rotation.y += 0.001;
    particlesMesh.rotation.x = targetY * 0.5;
    particlesMesh.rotation.z = targetX * 0.5;
    
    // Animate particles
    const positions = particlesGeometry.attributes.position.array;
    for (let i = 0; i < particlesCount; i++) {
        positions[i * 3 + 1] += speedArray[i] * Math.sin(elapsedTime + i);
        
        // Reset particle if it goes too high
        if (positions[i * 3 + 1] > 25) {
            positions[i * 3 + 1] = -25;
        }
    }
    particlesGeometry.attributes.position.needsUpdate = true;
    
    // Animate shapes
    shapes.forEach((shape, index) => {
        shape.rotation.x += shape.userData.rotationSpeed.x;
        shape.rotation.y += shape.userData.rotationSpeed.y;
        
        // Floating motion
        shape.position.y = shape.userData.originalY + 
            Math.sin(elapsedTime * shape.userData.floatSpeed + shape.userData.floatOffset) * 0.5;
    });
    
    // Animate heart
    heartMesh.rotation.z = Math.PI + Math.sin(elapsedTime * 2) * 0.1;
    heartMesh.scale.x = 1.5 + Math.sin(elapsedTime * 3) * 0.1;
    heartMesh.scale.y = 1.5 + Math.sin(elapsedTime * 3) * 0.1;
    
    // Rotate wireframe sphere
    wireframeSphere.rotation.x = elapsedTime * 0.2;
    wireframeSphere.rotation.y = elapsedTime * 0.3;
    
    // Animate ribbons
    ribbons.forEach((ribbon, index) => {
        ribbon.rotation.y = elapsedTime * 0.1 + index;
        ribbon.position.y = Math.sin(elapsedTime * 0.5 + index) * 2;
    });
    
    // Camera movement based on scroll
    camera.position.y = -scrollY * 0.01;
    camera.position.x += (targetX * 5 - camera.position.x) * 0.05;
    camera.lookAt(scene.position);
    
    // Pulse lights
    pointLight1.intensity = 2 + Math.sin(elapsedTime * 2) * 0.5;
    pointLight2.intensity = 2 + Math.cos(elapsedTime * 1.5) * 0.5;
    pointLight3.intensity = 1.5 + Math.sin(elapsedTime * 3) * 0.3;
    
    renderer.render(scene, camera);
}

animate();

// Handle window resize
window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
});

// Hide loading overlay when ready
window.addEventListener('load', () => {
    setTimeout(() => {
        document.getElementById('loadingOverlay').classList.add('hidden');
    }, 1000);
});

// Intersection Observer for fade-in animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

document.querySelectorAll('.fade-in').forEach(el => {
    observer.observe(el);
});
