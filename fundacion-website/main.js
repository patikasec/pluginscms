import * as THREE from 'three';

// Scene setup
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
const renderer = new THREE.WebGLRenderer({ 
    alpha: true, 
    antialias: true,
    powerPreference: 'high-performance'
});

renderer.setSize(window.innerWidth, window.innerHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
document.getElementById('canvas-container').appendChild(renderer.domElement);

// Create floating particles (representing hope and dreams)
const particlesGeometry = new THREE.BufferGeometry();
const particlesCount = 2000;

const posArray = new Float32Array(particlesCount * 3);
const colorsArray = new Float32Array(particlesCount * 3);
const sizesArray = new Float32Array(particlesCount);

const colorPalette = [
    new THREE.Color('#ff6b9d'), // Pink
    new THREE.Color('#ffa06b'), // Orange
    new THREE.Color('#ffd93d'), // Yellow
    new THREE.Color('#6b5bff'), // Purple
    new THREE.Color('#6bff9d'), // Green
];

for (let i = 0; i < particlesCount; i++) {
    posArray[i * 3] = (Math.random() - 0.5) * 50;
    posArray[i * 3 + 1] = (Math.random() - 0.5) * 50;
    posArray[i * 3 + 2] = (Math.random() - 0.5) * 50;

    const color = colorPalette[Math.floor(Math.random() * colorPalette.length)];
    colorsArray[i * 3] = color.r;
    colorsArray[i * 3 + 1] = color.g;
    colorsArray[i * 3 + 2] = color.b;

    sizesArray[i] = Math.random() * 0.5 + 0.1;
}

particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
particlesGeometry.setAttribute('color', new THREE.BufferAttribute(colorsArray, 3));
particlesGeometry.setAttribute('size', new THREE.BufferAttribute(sizesArray, 1));

// Custom shader material for particles
const particlesMaterial = new THREE.ShaderMaterial({
    vertexShader: `
        attribute float size;
        varying vec3 vColor;
        void main() {
            vColor = color;
            vec4 mvPosition = modelViewMatrix * vec4(position, 1.0);
            gl_PointSize = size * (300.0 / -mvPosition.z);
            gl_Position = projectionMatrix * mvPosition;
        }
    `,
    fragmentShader: `
        varying vec3 vColor;
        void main() {
            float distanceToCenter = distance(gl_PointCoord, vec2(0.5));
            if (distanceToCenter > 0.5) discard;
            float alpha = 1.0 - (distanceToCenter * 2.0);
            gl_FragColor = vec4(vColor, alpha);
        }
    `,
    vertexColors: true,
    transparent: true,
    blending: THREE.AdditiveBlending,
    depthWrite: false
});

const particles = new THREE.Points(particlesGeometry, particlesMaterial);
scene.add(particles);

// Create floating geometric shapes (representing building blocks of hope)
const shapes = [];
const shapeGeometries = [
    new THREE.IcosahedronGeometry(1, 0),
    new THREE.OctahedronGeometry(1, 0),
    new THREE.TetrahedronGeometry(1, 0),
    new THREE.TorusGeometry(0.7, 0.3, 8, 16),
];

const shapeMaterials = [
    new THREE.MeshPhongMaterial({ 
        color: 0xff6b9d, 
        transparent: true, 
        opacity: 0.6,
        shininess: 100
    }),
    new THREE.MeshPhongMaterial({ 
        color: 0xffa06b, 
        transparent: true, 
        opacity: 0.6,
        shininess: 100
    }),
    new THREE.MeshPhongMaterial({ 
        color: 0xffd93d, 
        transparent: true, 
        opacity: 0.6,
        shininess: 100
    }),
    new THREE.MeshPhongMaterial({ 
        color: 0x6b5bff, 
        transparent: true, 
        opacity: 0.6,
        shininess: 100
    }),
];

for (let i = 0; i < 15; i++) {
    const geometry = shapeGeometries[Math.floor(Math.random() * shapeGeometries.length)];
    const material = shapeMaterials[Math.floor(Math.random() * shapeMaterials.length)];
    const mesh = new THREE.Mesh(geometry, material);
    
    mesh.position.x = (Math.random() - 0.5) * 30;
    mesh.position.y = (Math.random() - 0.5) * 30;
    mesh.position.z = (Math.random() - 0.5) * 20 - 10;
    
    mesh.rotation.x = Math.random() * Math.PI;
    mesh.rotation.y = Math.random() * Math.PI;
    
    const scale = Math.random() * 0.8 + 0.3;
    mesh.scale.set(scale, scale, scale);
    
    mesh.userData = {
        rotationSpeed: {
            x: (Math.random() - 0.5) * 0.02,
            y: (Math.random() - 0.5) * 0.02
        },
        floatSpeed: Math.random() * 0.5 + 0.5,
        floatOffset: Math.random() * Math.PI * 2
    };
    
    shapes.push(mesh);
    scene.add(mesh);
}

// Create a central glowing sphere (representing the foundation's heart)
const heartGeometry = new THREE.SphereGeometry(2, 32, 32);
const heartMaterial = new THREE.MeshPhongMaterial({
    color: 0xff6b9d,
    emissive: 0xff6b9d,
    emissiveIntensity: 0.5,
    transparent: true,
    opacity: 0.8,
    shininess: 100
});
const heart = new THREE.Mesh(heartGeometry, heartMaterial);
heart.position.z = -5;
scene.add(heart);

// Add wireframe around the heart
const wireframeGeometry = new THREE.WireframeGeometry(heartGeometry);
const wireframeMaterial = new THREE.LineBasicMaterial({ 
    color: 0xffd93d,
    transparent: true,
    opacity: 0.3
});
const wireframe = new THREE.LineSegments(wireframeGeometry, wireframeMaterial);
heart.add(wireframe);

// Lighting
const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
scene.add(ambientLight);

const pointLight1 = new THREE.PointLight(0xff6b9d, 2, 50);
pointLight1.position.set(5, 5, 5);
scene.add(pointLight1);

const pointLight2 = new THREE.PointLight(0xffa06b, 2, 50);
pointLight2.position.set(-5, -5, 5);
scene.add(pointLight2);

const pointLight3 = new THREE.PointLight(0xffd93d, 1.5, 50);
pointLight3.position.set(0, 5, -5);
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

// Scroll interaction
let scrollY = 0;
document.addEventListener('scroll', () => {
    scrollY = window.scrollY;
});

// Animation loop
const clock = new THREE.Clock();

function animate() {
    requestAnimationFrame(animate);
    
    const elapsedTime = clock.getElapsedTime();
    
    targetX += (mouseX - targetX) * 0.05;
    targetY += (mouseY - targetY) * 0.05;
    
    // Rotate particles slowly
    particles.rotation.y += 0.0005;
    particles.rotation.x += 0.0002;
    
    // Animate particles with wave motion
    const positions = particlesGeometry.attributes.position.array;
    for (let i = 0; i < particlesCount; i++) {
        const i3 = i * 3;
        const originalY = positions[i3 + 1];
        positions[i3 + 1] += Math.sin(elapsedTime * 2 + positions[i3]) * 0.01;
    }
    particlesGeometry.attributes.position.needsUpdate = true;
    
    // Animate heart (pulse effect)
    const pulseScale = 1 + Math.sin(elapsedTime * 2) * 0.05;
    heart.scale.set(pulseScale, pulseScale, pulseScale);
    heart.rotation.y += 0.005;
    heart.rotation.x += 0.003;
    
    // Animate shapes
    shapes.forEach((shape, index) => {
        shape.rotation.x += shape.userData.rotationSpeed.x;
        shape.rotation.y += shape.userData.rotationSpeed.y;
        
        // Floating motion
        shape.position.y += Math.sin(elapsedTime * shape.userData.floatSpeed + shape.userData.floatOffset) * 0.02;
    });
    
    // Camera movement based on mouse and scroll
    camera.position.x += (targetX * 5 - camera.position.x) * 0.05;
    camera.position.y += (-targetY * 5 - camera.position.y) * 0.05;
    camera.position.z = 15 + Math.sin(scrollY * 0.002) * 3;
    
    camera.lookAt(scene.position);
    
    renderer.render(scene, camera);
}

animate();

// Handle window resize
window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
});

// Add touch support for mobile devices
document.addEventListener('touchmove', (event) => {
    if (event.touches.length > 0) {
        mouseX = (event.touches[0].clientX - windowHalfX) * 0.001;
        mouseY = (event.touches[0].clientY - windowHalfY) * 0.001;
    }
});
